<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for numeric lower bounds.
 *
 * Use when a numeric field must be greater than or equal to a minimum value.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Min extends AbstractConstraint
{
    /**
     * Create a new Min constraint.
     *
     * @param float $min Minimum allowed value (inclusive)
     * @param string|null $message Custom error message
     */
    public function __construct(public float $min, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate minimum value.
     *
     * @param Validation $validation Validation context
     * @return bool True if value >= min, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        return $validation->value >= $this->min;
    }
}
