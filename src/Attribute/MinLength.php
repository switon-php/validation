<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for minimum string length.
 *
 * Use when a field must contain at least the required number of characters.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class MinLength extends AbstractConstraint
{
    /**
     * Create a new MinLength constraint.
     *
     * @param int $min Minimum required length
     * @param string|null $message Custom error message
     */
    public function __construct(public int $min, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate minimum string length.
     *
     * Uses mb_strlen() for proper Unicode character counting.
     *
     * @param Validation $validation Validation context
     * @return bool True if length >= min, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        return mb_strlen($validation->value) >= $this->min;
    }
}
