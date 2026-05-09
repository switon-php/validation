<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function strtolower;

/**
 * Validation constraint attribute for email format checks.
 *
 * Use when a field must be a valid email address and should be normalized to lowercase.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Email extends AbstractConstraint
{
    /**
     * Validate email format and normalize to lowercase.
     *
     * This method:
     * 1. Validates email format using PHP's FILTER_VALIDATE_EMAIL
     * 2. Converts email to lowercase for consistency
     * 3. Modifies $validation->value with the lowercase email
     *
     * @param Validation $validation Validation context
     * @return bool True if valid email format, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Validate email format using PHP's built-in filter
        if (filter_var($validation->value, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        // Normalize email to lowercase (email addresses are case-insensitive)
        $validation->value = strtolower($validation->value);

        return true;
    }
}
