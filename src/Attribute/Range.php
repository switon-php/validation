<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for inclusive numeric ranges.
 *
 * Use when a numeric field must stay between minimum and maximum bounds.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Range extends AbstractConstraint
{
    /**
     * Create a new Range constraint.
     *
     * @param int $min Minimum allowed value (inclusive)
     * @param int $max Maximum allowed value (inclusive)
     * @param string|null $message Custom error message
     */
    public function __construct(public int $min, public int $max, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate that value is within the specified range.
     *
     * @param Validation $validation Validation context
     * @return bool True if value >= min AND value <= max, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Check if value is within range (inclusive on both ends)
        return $validation->value >= $this->min && $validation->value <= $this->max;
    }
}
