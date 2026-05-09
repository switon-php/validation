<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for IP address format checks.
 *
 * Use when a field must be a valid IPv4 or IPv6 address.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Ip extends AbstractConstraint
{
    /**
     * Validate IP address format.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid IP address (IPv4 or IPv6), false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Validate IP address using PHP's built-in filter (supports IPv4 and IPv6)
        return filter_var($validation->value, FILTER_VALIDATE_IP) !== false;
    }
}
