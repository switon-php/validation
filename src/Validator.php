<?php

declare(strict_types=1);

namespace Switon\Validating;

use RuntimeException;
use Switon\Core\Attribute\Autowired;
use Switon\Core\ClassName;
use Switon\Core\FilesystemInterface;
use Switon\Core\Lazy;
use Switon\Core\Json;
use Switon\Core\LocaleInterface;
use Switon\Core\TranslatorInterface;
use Switon\Validating\Exception\LocaleTemplateNotFoundException;
use Switon\Validating\Exception\ValidateFailedException;
use function in_array;
use function is_array;
use function is_string;
use function pathinfo;
use function str_ends_with;
use function strtolower;
use function str_contains;
use function strtr;

/**
 * Default validator implementation with constraint execution and localized messages.
 *
 * Use when requests, typed inputs, or entities need field-level validation with consistent error formatting.
 *
 * @see \Switon\Validating\ValidatorInterface
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\ConstraintInterface
 * @see \Switon\Validating\Exception\ValidateFailedException
 * @see \Switon\Validating\Exception\ConstraintViolationException
 * @see \Switon\Validating\Exception\LocaleTemplateNotFoundException
 */
class Validator implements ValidatorInterface
{
    /**
     * Locale provider for internationalization.
     *
     * @var LocaleInterface|Lazy Locale instance or lazy proxy
     */
    #[Autowired] protected LocaleInterface|Lazy $locale;

    #[Autowired] protected TranslatorInterface|Lazy $translator;

    /**
     * Filesystem interface for loading template files.
     *
     * @var FilesystemInterface Filesystem instance
     */
    #[Autowired] protected FilesystemInterface $filesystem;

    /**
     * Validation template directories in merge order.
     *
     * Later directories override earlier ones for the same locale keys.
     *
     * @var list<string>
     */
    #[Autowired] protected array $dirs = [];

    /**
     * Map of locale names to ordered template file paths.
     *
     * Structure: ['en' => ['/path/base/en.php', '/path/app/en.php']]
     *
     * @var array<string, list<string>>
     */
    protected array $files = [];

    /**
     * Cached validation message templates by locale.
     *
     * Structure: ['en' => ['required' => 'Field is required', 'labels' => [...]], ...]
     *
     * @var array<string, array<string, mixed>> Cached templates
     */
    protected array $templates = [];

    /**
     * Locale code for message templates when the requested locale has no file.
     *
     * @var string Fallback locale code (default: 'en')
     */
    #[Autowired] protected string $fallback = 'en';

    /**
     * Whether explicit constraint labels should be treated as translation keys.
     */
    #[Autowired] protected bool $translateLabel = false;

    /**
     * Whether field labels should fall back to standard i18n translation keys.
     */
    #[Autowired] protected bool $translateField = true;

    /**
     * Initializes the validator and loads available template files.
     *
     * Scans the template directories and builds a map of available locales.
     * Template files should be named with lowercase locale codes (e.g., en.php, zh-cn.php).
     *
     * **Performance:** O(n) where n is number of template files (typically <10)
     */
    public function __construct()
    {
        foreach ($this->getTemplateDirectories() as $dir) {
            foreach ($this->filesystem->glob($dir . '/*.php') as $file) {
                $locale = strtolower(pathinfo($file, PATHINFO_FILENAME));
                $this->files[$locale] ??= [];
                $this->files[$locale][] = $file;
            }
        }
    }

    /**
     * Return validation template directories in merge order.
     *
     * @return list<string>
     */
    protected function getTemplateDirectories(): array
    {
        $directories = [];

        foreach ($this->dirs as $dir) {
            if (!is_string($dir) || $dir === '' || in_array($dir, $directories, true)) {
                continue;
            }

            $directories[] = $dir;
        }

        return $directories;
    }

    /**
     * Resolves the best matching template file for a given locale.
     *
     * Uses a fallback strategy:
     * 1. Try exact locale match (e.g., 'en-US')
     * 2. Try language part only (e.g., 'en-US' -> 'en')
     * 3. Fall back to default locale
     * 4. Return null if no match found
     *
     * **Locale Resolution Examples:**
     * - 'en-US' with 'en-us.php' available -> 'en-us'
     * - 'en-US' with only 'en.php' available -> 'en'
     * - 'fr-FR' with no French templates -> 'en' (default)
     * - 'unknown' with no default -> null
     *
     * **Performance:** O(1) hash map lookups
     *
     * @param string $locale Requested locale code (e.g., 'en-US', 'zh-CN')
     * @return string|null Resolved locale key or null if no match found
     */
    protected function resolveLocaleFile(string $locale): ?string
    {
        $locale = $this->normalizeLocale($locale);

        // Step 1: Try exact locale match first
        if ($locale !== '' && isset($this->files[$locale])) {
            return $locale;
        }

        // Step 2: Progressively trim region/variant parts
        // Example: 'zh-Hans-CN' -> 'zh-hans' -> 'zh'
        $candidate = $locale;
        while (($pos = strrpos($candidate, '-')) !== false) {
            $candidate = substr($candidate, 0, $pos);
            if (isset($this->files[$candidate])) {
                return $candidate;
            }
        }

        // Step 3: Fallback to default locale
        // Ensures validation messages are always available
        $fallback = $this->normalizeLocale($this->fallback);
        if ($fallback !== '' && isset($this->files[$fallback])) {
            return $fallback;
        }

        // Step 4: No template file found
        // This should rarely happen in production
        return null;
    }

    /**
     * Normalizes a locale code for template lookup.
     *
     * Converts underscore separators to hyphens and lowercases the value so
     * common forms like <code>zh-CN</code> and <code>zh_CN</code> resolve to the
     * same template key.
     */
    protected function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', trim($locale)));
    }

    /**
     * Gets the validation message template for a constraint.
     *
     * Loads and caches templates on first access per locale. Templates are
     * PHP files that return an array of message templates.
     *
     * **Template Structure:**
     * <code>
     * return [
     *     'required' => 'The {field} field is required.',
     *     'email' => 'The {field} must be a valid email address.',
     *     'default' => 'The {field} is invalid.'
     * ];
     * </code>
     *
     * **Performance:**
     * - First call per locale: O(1) file load + cache
     * - Subsequent calls: O(1) cached lookup
     *
     * @param string $message Constraint FQCN (template key) or custom message template
     * @return string|callable Message template string or callable
     * @throws RuntimeException When no template file found for locale
     */
    protected function getTemplate(string $message): string|callable
    {
        // If message is not a FQCN (no backslash), treat as custom message template
        // This allows constraints to provide inline messages like "The {field} is too short"
        if (!str_contains($message, '\\')) {
            return $message;
        }

        $templates = $this->getLocaleData();

        // Return specific template or fallback to default
        return $templates[$message] ?? $templates['default'];
    }

    /**
     * Return locale data for the current validation locale.
     *
     * @return array<string, mixed>
     */
    protected function getLocaleData(): array
    {
        $locale = $this->locale->get();
        $resolvedLocale = $this->resolveLocaleFile($locale);

        if ($resolvedLocale === null) {
            LocaleTemplateNotFoundException::raise('Validation template not found for locale "{locale}"', ['locale' => $locale]);
        }

        if (!isset($this->templates[$resolvedLocale])) {
            $this->templates[$resolvedLocale] = $this->loadTemplatesForLocale($resolvedLocale);
        }

        return $this->templates[$resolvedLocale];
    }

    /**
     * Load and merge all template files for one locale.
     *
     * @return array<string, mixed>
     */
    protected function loadTemplatesForLocale(string $locale): array
    {
        $merged = [];

        foreach ($this->files[$locale] ?? [] as $file) {
            $templates = require $file;
            if (!is_array($templates)) {
                continue;
            }

            $merged = $this->mergeTemplateData($merged, $templates);
        }

        return $merged;
    }

    /**
     * Merge validation template data recursively.
     *
     * Later template files override earlier ones. Nested label maps are merged by key.
     *
     * @param array<string, mixed> $base
     * @param array<string, mixed> $patch
     * @return array<string, mixed>
     */
    protected function mergeTemplateData(array $base, array $patch): array
    {
        foreach ($patch as $key => $value) {
            if (isset($base[$key]) && is_array($base[$key]) && is_array($value)) {
                $base[$key] = $this->mergeTemplateData($base[$key], $value);
                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }

    /**
     * Return configured template-level field labels for the current locale.
     *
     * @return array<string, string>
     */
    protected function getTemplateLabels(): array
    {
        $labels = $this->getLocaleData()['labels'] ?? [];

        return is_array($labels) ? $labels : [];
    }

    /**
     * Resolve a display label for one field/path.
     *
     * Lookup order:
     * 1. Explicit constraint labels
     * 2. Template-level labels
     * 3. Standard i18n keys
     * 4. Raw field path
     *
     * @param string $field Field path or name
     * @param array<string, string> $labels Explicit label overrides
     * @param class-string|null $sourceClass Current source class
     */
    protected function resolveFieldLabel(string $field, array $labels = [], ?string $sourceClass = null): string
    {
        $classField = $this->buildClassFieldKey($sourceClass, $field);

        if ($classField !== null && isset($labels[$classField])) {
            return $this->normalizeExplicitLabel($labels[$classField]);
        }

        if (isset($labels[$field])) {
            return $this->normalizeExplicitLabel($labels[$field]);
        }

        $templateLabels = $this->getTemplateLabels();
        if ($classField !== null && isset($templateLabels[$classField])) {
            return $templateLabels[$classField];
        }

        if (isset($templateLabels[$field])) {
            return $templateLabels[$field];
        }

        if ($this->translateField) {
            if ($classField !== null && ($translated = $this->translateLabelKey('validation.labels.' . $classField)) !== null) {
                return $translated;
            }

            if (($translated = $this->translateLabelKey('validation.labels.' . $field)) !== null) {
                return $translated;
            }
        }

        return $field;
    }

    /**
     * Convert an explicit constraint label to its final display string.
     */
    protected function normalizeExplicitLabel(string $label): string
    {
        if (!$this->translateLabel) {
            return $label;
        }

        return $this->translator->has($label) ? $this->translator->translate($label) : $label;
    }

    /**
     * Translate one standard field-label key in quiet mode.
     */
    protected function translateLabelKey(string $key): ?string
    {
        if (!$this->translator->has($key)) {
            return null;
        }

        $translated = $this->translator->translate($key);
        return $translated === $key ? null : $translated;
    }

    /**
     * Build the short-class field key used by labels and i18n fallback.
     *
     * @param class-string|null $sourceClass
     */
    protected function buildClassFieldKey(?string $sourceClass, string $field): ?string
    {
        if ($sourceClass === null || $sourceClass === '') {
            return null;
        }

        return ClassName::short($sourceClass) . '::' . $field;
    }

    /**
     * Resolve special validation placeholders such as field references.
     *
     * @param array<string, string> $labels
     * @param class-string|null $sourceClass
     */
    protected function resolvePlaceholderValue(
        string  $key,
        mixed   $value,
        array   $labels = [],
        ?string $sourceClass = null
    ): mixed
    {
        if ($key === 'field' && is_string($value)) {
            return $this->resolveFieldLabel($value, $labels, $sourceClass);
        }

        if ($key === 'fields' && is_array($value)) {
            $resolved = [];
            foreach ($value as $field) {
                $resolved[] = is_string($field)
                    ? $this->resolveFieldLabel($field, $labels, $sourceClass)
                    : Json::stringify($field);
            }

            return implode(' + ', $resolved);
        }

        if (str_ends_with($key, 'Field') && is_string($value)) {
            return $this->resolveFieldLabel($value, $labels, $sourceClass);
        }

        return $value;
    }

    /**
     * Validates multiple values against their constraints.
     *
     * Validates each field in the source data against its constraints.
     * Stops validation for a field on first error. Returns only valid values.
     *
     * **Validation Flow:**
     * 1. Create validation context
     * 2. For each field:
     *    - Set field and value in context
     *    - Apply constraints in order
     *    - Stop on first error
     *    - Collect valid value if no errors
     * 3. Throw exception if any errors
     *
     * **Data Integrity:**
     * Missing fields (not present in source) are NOT included in the return value
     * unless a constraint explicitly sets a value (e.g., Defaults). This preserves
     * the distinction between "field is null" and "field is absent", preventing
     * downstream logic (e.g., ORM) from overwriting database values with NULL.
     *
     * **Performance:** O(n*m) where n=fields, m=constraints per field
     *
     * @param array<string, mixed> $source Source data to validate
     * @param array<string, ConstraintInterface|ConstraintInterface[]> $constraints Field constraints
     * @return array<string, mixed> Validated and possibly transformed values
     * @throws ValidateFailedException When validation fails for any field
     */
    public function validateValues(array $source, array $constraints): array
    {
        // Create validation context
        $validation = $this->beginValidate($source);

        // Collect validated values
        $values = [];

        // Validate each field
        foreach ($constraints as $field => $fieldConstraints) {
            // Set current field and value in validation context
            $validation->field = $field;
            $validation->value = $source[$field] ?? null;
            $exists = array_key_exists($field, $source);

            // Apply constraints (single constraint or array of constraints)
            // Performance: O(m) where m is number of constraints for this field
            foreach (is_array($fieldConstraints) ? $fieldConstraints : [$fieldConstraints] as $constraint) {
                // Validate with constraint
                // Stops on first error (short-circuit evaluation)
                if (!$validation->validate($constraint)) {
                    break; // Skip remaining constraints for this field
                }
            }

            // Collect value if validation passed
            // Only include if: field existed in source, or constraint modified the value (e.g., Defaults)
            // This prevents injecting null for missing fields into the output
            if (!$validation->hasError($field) && ($exists || $validation->value !== null)) {
                $values[$field] = $validation->value;
            }
        }

        // Throw exception if any validation errors occurred
        // This ensures all-or-nothing validation semantics
        $this->endValidate($validation);

        return $values;
    }

    /**
     * Validates a single value against constraints.
     *
     * Convenience method for validating a single field. Internally uses
     * validateValues() with a single-field array.
     *
     * **Performance:** Same as validateValues() for single field
     *
     * @param string $field Field name (used in error messages)
     * @param mixed $value Value to validate
     * @param ConstraintInterface|ConstraintInterface[] $constraints Constraint(s) to apply
     * @return mixed Validated and possibly transformed value, or null if validation failed
     * @throws ValidateFailedException When validation fails
     */
    public function validateValue(string $field, mixed $value, array $constraints): mixed
    {
        return $this->validateValues([$field => $value], [$field => $constraints])[$field] ?? null;
    }

    /**
     * Begins a manual validation session.
     *
     * Creates a validation context for manual validation flow. Use this when
     * you need fine-grained control over validation process.
     *
     * **Manual Validation Example:**
     * <code>
     * $validation = $validator->beginValidate($data);
     * $validation->field = 'email';
     * $validation->value = $data['email'];
     *
     * if (!$validation->validate(new Email())) {
     *     // Handle error
     * }
     *
     * $validator->endValidate($validation); // Throws if errors
     * </code>
     *
     * **Performance:** O(1) object creation
     *
     * @param array<string, mixed>|object $source Source data (array or object)
     * @return Validation Validation context for manual validation
     */
    public function beginValidate(array|object $source): Validation
    {
        return new Validation($this, $source);
    }

    /**
     * Ends a validation session and throws if errors exist.
     *
     * Finalizes validation and throws ValidateFailedException if any
     * validation errors were collected during the session.
     *
     * **Performance:** O(1) error check
     *
     * @param Validation $validation Validation context to finalize
     * @return void
     * @throws ValidateFailedException When validation errors exist
     */
    public function endValidate(Validation $validation): void
    {
        // Check if any validation errors occurred
        if (($errors = $validation->getErrors()) !== []) {
            // Throw exception with all collected errors
            // Exception contains field-to-message mapping
            ValidateFailedException::raiseForValidationFailed($errors);
        }
    }

    /**
     * Formats a validation message with placeholders.
     *
     * Replaces placeholders in message template with actual values.
     * Placeholders use {key} syntax and are replaced with values from
     * the placeholders array.
     *
     * **Placeholder Formatting:**
     * - String values: Inserted as-is
     * - Non-string values: JSON-encoded
     * - Missing placeholders: Left unchanged
     *
     * **Example:**
     * <code>
     * $message = $validator->formatMessage('required', ['field' => 'email']);
     * // Result: "The email field is required."
     *
     * $message = $validator->formatMessage('between', [
     *     'field' => 'age',
     *     'min' => 18,
     *     'max' => 65
     * ]);
     * // Result: "The age must be between 18 and 65."
     * </code>
     *
     * **Performance:** O(n) where n is number of placeholders
     *
     * @param string $message Message template name or constraint class
     * @param array<string, mixed> $placeholders Placeholder values
     * @return string Formatted message with placeholders replaced
     */
    public function formatMessage(string $message, array $placeholders = []): string
    {
        // Get message template for this constraint
        // Performance: O(1) cached lookup after first load
        $template = $this->getTemplate($message);

        /** @var array<string, string> $labels */
        $labels = is_array($placeholders['_labels'] ?? null) ? $placeholders['_labels'] : [];
        $sourceClass = is_string($placeholders['_sourceClass'] ?? null)
            ? $placeholders['_sourceClass']
            : null;
        unset($placeholders['_labels'], $placeholders['_sourceClass']);

        // Build replacement map for strtr()
        // Format: ['{key}' => 'value', ...]
        // Performance: O(n) where n is number of placeholders
        $replaces = [];
        foreach ($placeholders as $key => $value) {
            $value = $this->resolvePlaceholderValue($key, $value, $labels, $sourceClass);

            // Convert non-string values to JSON for display
            // This handles arrays, objects, numbers, etc.
            $replaces['{' . $key . '}'] = is_string($value) ? $value : Json::stringify($value);
        }

        // Replace all placeholders in template
        // Performance: O(m) where m is template length
        return strtr($template, $replaces);
    }
}
