<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function strtolower;

/**
 * Validation constraint attribute for lowercase normalization or checks.
 *
 * Use when string input should be converted to lowercase or validated as lowercase.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Lowercase extends AbstractConstraint
{
    /**
     * Create a new Lowercase constraint.
     *
     * @param bool $sanitize If true, converts to lowercase; if false, validates lowercase
     * @param string|null $message Custom error message
     */
    public function __construct(protected bool $sanitize = true, protected ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate or sanitize to lowercase.
     *
     * @param Validation $validation Validation context
     * @return bool True if sanitizing or value is lowercase, false if validating and not lowercase
     */
    public function validate(Validation $validation): bool
    {
        if ($this->sanitize) {
            // Sanitize mode: convert to lowercase and always pass
            $validation->value = strtolower($validation->value);
            return true;
        } else {
            // Validate mode: check if already lowercase
            return $validation->value === strtolower($validation->value);
        }
    }
}
