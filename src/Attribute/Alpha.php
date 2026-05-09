<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for alphabetic strings.
 *
 * Use when a field must contain letters only.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Alpha extends AbstractConstraint
{
    /**
     * Validate alphabetic format.
     *
     * @param Validation $validation Validation context
     * @return bool True if value contains only letters, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: one or more letters only (no digits or special chars)
        return preg_match('#^[a-zA-Z]+$#', $validation->value) === 1;
    }
}
