<?php

declare(strict_types=1);

namespace Switon\Validating;

/**
 * Base class for validation constraint attributes with shared message-key behavior.
 *
 * Use when implementing custom constraints that reuse the default
 * <code>message ?? static::class</code> fallback contract and optional field-label overrides.
 *
 * Road-signs:
 * - getMessage(): custom message or constraint-class template key
 * - getLabels(): per-field display-label overrides
 *
 * @see \Switon\Validating\ConstraintInterface
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\Exception\ConstraintViolationException
 */
abstract class AbstractConstraint implements ConstraintInterface, LabelAwareConstraintInterface
{
    /**
     * Create a new constraint instance.
     *
     * @param string|null $label Display label for the current field
     * @param array<string, string> $labels Display labels for related fields
     * @param string|null $message Custom error message (null to use class name as key)
     */
    public function __construct(
        protected ?string $label = null,
        protected array   $labels = [],
        protected ?string $message = null,
    ) {

    }

    /**
     * Get the error message for this constraint.
     *
     * Returns the custom message if provided; otherwise returns the constraint
     * class name so validator templates can resolve a locale-specific message.
     *
     * @return string Error message or message key
     */
    public function getMessage(): string
    {
        // Use custom message if provided, otherwise use class name as message key
        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        return $this->message ?? static::class;
    }

    /**
     * Return display-label overrides keyed by field name.
     *
     * When `$label` is configured for the current field, it is merged into the
     * returned map without overwriting an explicit `$labels[$field]` entry.
     *
     * @param string|null $field Current field name/path when available
     *
     * @return array<string, string>
     */
    public function getLabels(?string $field = null): array
    {
        $labels = $this->labels;

        if ($field !== null && $this->label !== null && !isset($labels[$field])) {
            $labels[$field] = $this->label;
        }

        return $labels;
    }
}
