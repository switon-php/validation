<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function is_array;
use function is_object;

/**
 * Validation constraint attribute for cross-field equality checks.
 *
 * Use when one field must exactly match another field value, such as password confirmation.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class EqualTo extends AbstractConstraint
{
    /**
     * Create a new EqualTo constraint.
     *
     * @param string $otherField Name of the field to compare against
     * @param string|null $label Display label for the current field
     * @param array<string, string> $labels Display labels for related fields
     * @param string|null $message Custom error message
     */
    public function __construct(
        public string     $otherField,
        protected ?string $label = null,
        protected array   $labels = [],
        public ?string    $message = null,
    )
    {
        parent::__construct($label, $labels, $message);
    }

    /**
     * Validate that value equals another field's value.
     *
     * Supports both array and object sources. Uses strict comparison (===).
     *
     * @param Validation $validation Validation context
     * @return bool True if values are equal, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        $source = $validation->source;
        $otherValue = null;

        // Extract other field value from source
        if (is_array($source)) {
            $otherValue = $source[$this->otherField] ?? null;
        } elseif (is_object($source)) {
            // For objects, access public properties directly
            // This works for request body objects which have public properties
            if (property_exists($source, $this->otherField)) {
                $otherValue = $source->{$this->otherField};
            }
        }

        // Strict comparison for type safety
        return $validation->value === $otherValue;
    }

}
