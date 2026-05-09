<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for mainland China mobile numbers.
 *
 * Use when a field must follow the 11-digit mobile pattern that starts with 13-19.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Mobile extends AbstractConstraint
{
    /**
     * Validate Chinese mobile phone number format.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid Chinese mobile number, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: starts with 1[3-9], followed by 9 digits (total 11 digits)
        return preg_match('#^1[3-9]\d{9}$#', $validation->value) === 1;
    }
}
