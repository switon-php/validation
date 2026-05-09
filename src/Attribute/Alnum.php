<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for alphanumeric strings.
 *
 * Use when a field must contain only letters and digits.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Alnum extends AbstractConstraint
{
    /**
     * Validate alphanumeric format.
     *
     * @param Validation $validation Validation context
     * @return bool True if value contains only letters and digits, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: one or more letters or digits only
        return preg_match('#^[a-zA-Z\d]+$#', $validation->value) === 1;
    }
}
