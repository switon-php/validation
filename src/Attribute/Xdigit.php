<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for hexadecimal strings.
 *
 * Use when a field must contain hexadecimal digits only.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Xdigit extends AbstractConstraint
{
    /**
     * Validate hexadecimal digit format.
     *
     * @param Validation $validation Validation context
     * @return bool True if value contains only hex digits, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Pattern: one or more hexadecimal digits (0-9, a-f, A-F)
        return preg_match('#^[\da-fA-F]+$#', $validation->value) === 1;
    }
}
