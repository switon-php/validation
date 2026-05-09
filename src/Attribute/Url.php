<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for URL format checks.
 *
 * Use when a field must be a valid URL including scheme.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Url extends AbstractConstraint
{
    /**
     * Validate URL format.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid URL format, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Validate URL format using PHP's built-in filter
        return filter_var($validation->value, FILTER_VALIDATE_URL) !== false;
    }
}
