<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for maximum string length.
 *
 * Use when a field must not exceed a configured character limit.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class MaxLength extends AbstractConstraint
{
    /**
     * Create a new MaxLength constraint.
     *
     * @param int $max Maximum allowed length
     * @param string|null $message Custom error message
     */
    public function __construct(public int $max, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate maximum string length.
     *
     * Uses mb_strlen() for proper Unicode character counting.
     *
     * @param Validation $validation Validation context
     * @return bool True if length <= max, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        return mb_strlen($validation->value) <= $this->max;
    }
}
