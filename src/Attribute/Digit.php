<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for digit-only strings.
 *
 * Use when a field must contain decimal digits only.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Digit extends AbstractConstraint
{
    /**
     * Validate digit-only format.
     *
     * @param Validation $validation Validation context
     * @return bool True if value contains only digits, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: one or more digits only
        return preg_match('#^\d+$#', $validation->value) === 1;
    }
}
