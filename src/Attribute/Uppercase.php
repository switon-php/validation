<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function strtoupper;

/**
 * Validation constraint attribute for uppercase normalization or checks.
 *
 * Use when string input should be converted to uppercase or validated as uppercase.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Uppercase extends AbstractConstraint
{
    /**
     * Create a new Uppercase constraint.
     *
     * @param bool $sanitize If true, converts to uppercase; if false, validates uppercase
     * @param string|null $message Custom error message
     */
    public function __construct(protected bool $sanitize = true, protected ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate or sanitize to uppercase.
     *
     * @param Validation $validation Validation context
     * @return bool True if sanitizing or value is uppercase, false if validating and not uppercase
     */
    public function validate(Validation $validation): bool
    {
        if ($this->sanitize) {
            // Sanitize mode: convert to uppercase and always pass
            $validation->value = strtoupper($validation->value);
            return true;
        } else {
            // Validate mode: check if already uppercase
            return $validation->value === strtoupper($validation->value);
        }
    }
}
