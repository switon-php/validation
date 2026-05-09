<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for non-empty values.
 *
 * Use when a field must be set and must not be an empty string.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class NotEmpty extends AbstractConstraint
{
    /**
     * Validate that value is not empty.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is set and not empty string, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Check if value is set AND not an empty string
        return isset($validation->value) && $validation->value !== '';
    }
}
