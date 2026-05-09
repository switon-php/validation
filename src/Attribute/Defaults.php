<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for default-value fallback.
 *
 * Use when null or empty-string input should be replaced with a predefined default value.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Defaults extends AbstractConstraint
{
    /**
     * Create a new Defaults constraint.
     *
     * @param mixed $default Default value to use when field is null or empty
     * @param string|null $message Custom error message
     */
    public function __construct(public mixed $default, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Apply default value if field is null or empty string.
     *
     * This constraint always returns true (never fails) as it's a sanitizer.
     * Modifies $validation->value with the default if needed.
     *
     * @param Validation $validation Validation context
     * @return bool Always returns true
     */
    public function validate(Validation $validation): bool
    {
        // Apply default value if field is null or empty string
        if ($validation->value === null || $validation->value === '') {
            $validation->value = $this->default;
        }

        // Always passes validation (this is a sanitizer, not a validator)
        return true;
    }
}
