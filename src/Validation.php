<?php

declare(strict_types=1);

namespace Switon\Validating;

use function is_object;

/**
 * Holds mutable validation state for a single validation session.
 *
 * Use when constraints must share the current field/value/source and collect errors in one
 * context object during batch or manual validation.
 *
 * @see \Switon\Validating\ValidatorInterface
 * @see \Switon\Validating\ValidatorInterface::beginValidate()
 * @see \Switon\Validating\ValidatorInterface::endValidate()
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\ConstraintInterface
 * @see \Switon\Validating\ConstraintInterface::validate()
 * @see \Switon\Validating\Attribute\ArrayOf Array element typing for typed-input binding
 * @see \Switon\Http\RequestBodyResolver::resolve() Attribute-driven validation session (typed-input properties)
 */
class Validation
{
    /**
     * Collected validation errors.
     *
     * Format: ['fieldName' => 'error message', ...]
     *
     * @var array<string, string>
     */
    protected array $errors = [];

    /**
     * Current field being validated.
     *
     * @var string
     */
    public string $field;

    /**
     * Current value being validated.
     *
     * Note: This value can be modified by constraints (e.g., Email converts to lowercase).
     *
     * @var mixed
     */
    public mixed $value;

    /**
     * Declared target type for the current field when known.
     *
     * Framework entrypoints such as typed-input binders can set this so constraints
     * can normalize raw input before type validation runs.
     *
     * @var string|null
     */
    public ?string $targetType = null;

    /**
     * Current source class for field-label resolution.
     *
     * This may be null for array-only validation sessions.
     *
     * @var class-string|null
     */
    public ?string $sourceClass = null;

    /**
     * Validator instance for message formatting.
     *
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * Create a new validation context.
     *
     * @param ValidatorInterface $validator Validator instance for message formatting
     * @param array|object $source Source data being validated
     */
    public function __construct(ValidatorInterface $validator, public array|object $source)
    {
        $this->validator = $validator;
        $this->sourceClass = is_object($source) ? $source::class : null;
    }

    /**
     * Validate the current field/value against a constraint.
     *
     * This method:
     * 1. Skips validation if the field already has an error (fail-fast)
     * 2. Executes the constraint's validation logic
     * 3. Adds an error message if validation fails
     * 4. Returns whether the field is still valid
     *
     * @param ConstraintInterface $constraint Constraint to validate against
     * @return bool True if validation passed, false if failed
     */
    public function validate(ConstraintInterface $constraint): bool
    {
        // Skip validation if field already has an error (fail-fast behavior)
        if (isset($this->errors[$this->field])) {
            return false;
        }

        // Execute constraint validation
        if (!$constraint->validate($this) && !isset($this->errors[$this->field])) {
            // Validation failed and constraint didn't add its own error - use default error
            $labels = method_exists($constraint, 'getLabels')
                ? $constraint->getLabels($this->field)
                : [];
            $this->addError($constraint->getMessage(), (array)$constraint, $labels);
        }

        // Return true only if no error exists for this field
        return !isset($this->errors[$this->field]);
    }

    /**
     * Add a validation error for the current field.
     *
     * The error message can contain placeholders that will be replaced with values
     * from the $placeholders array. The 'field' placeholder is automatically added.
     *
     * @param string $message Error message template (e.g., "{field} must be between {min} and {max}")
     * @param array<string, mixed> $placeholders Placeholder values for message formatting
     * @param array<string, string> $labels Display labels keyed by field name
     * @return void
     */
    public function addError(string $message, array $placeholders = [], array $labels = []): void
    {
        // Automatically add field name to placeholders
        $placeholders['field'] = $this->field;
        $placeholders['_labels'] = $labels;
        $placeholders['_sourceClass'] = $this->sourceClass;

        // Format message and store error
        $this->errors[$this->field] = $this->validator->formatMessage($message, $placeholders);
    }

    /**
     * Get all validation errors.
     *
     * @return array<string, string> Array of errors (field => message)
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if a specific field has a validation error.
     *
     * @param string $field Field name to check
     * @return bool True if field has an error, false otherwise
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
}
