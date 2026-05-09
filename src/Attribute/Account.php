<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for account-style usernames.
 *
 * Use when a field must start with a lowercase letter and continue with lowercase letters, digits, or underscores.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Account extends AbstractConstraint
{
    /**
     * Validate account format.
     *
     * @param Validation $validation Validation context
     * @return bool True if value matches account pattern, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: starts with lowercase letter, followed by 2+ lowercase/digits/underscores
        return preg_match('#^[a-z][a-z\d_]{2,}$#', $validation->value) === 1;
    }
}
