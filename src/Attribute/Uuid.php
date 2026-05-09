<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for UUID format checks.
 *
 * Use when a field must be a canonical UUID string with hyphen separators.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Uuid extends AbstractConstraint
{
    /**
     * Validate UUID format.
     *
     * Pattern: 8 hex digits, hyphen, 4 hex digits (x3), hyphen, 12 hex digits
     *
     * @param Validation $validation Validation context
     * @return bool True if valid UUID format, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (case-insensitive)
        return preg_match('#^[\da-f]{8}(-[\da-f]{4}){3}-[\da-f]{12}$#i', $validation->value) === 1;
    }
}
