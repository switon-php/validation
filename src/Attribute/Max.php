<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for numeric upper bounds.
 *
 * Use when a numeric field must be less than or equal to a maximum value.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Max extends AbstractConstraint
{
    /**
     * Create a new Max constraint.
     *
     * @param float $max Maximum allowed value (inclusive)
     * @param string|null $message Custom error message
     */
    public function __construct(public float $max, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate maximum value.
     *
     * @param Validation $validation Validation context
     * @return bool True if value <= max, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        return $validation->value <= $this->max;
    }
}
