<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function is_string;
use function str_starts_with;

/**
 * Validation constraint attribute for prefix matching.
 *
 * Use when a string field must start with one or more required prefixes.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class StartsWith extends AbstractConstraint
{
    /**
     * Create a new StartsWith constraint.
     *
     * @param array|string $needles Single prefix or array of prefixes
     * @param string|null $message Custom error message
     */
    public function __construct(public array|string $needles, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate that value starts with one of the specified prefixes.
     *
     * @param Validation $validation Validation context
     * @return bool True if value starts with any of the needles, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Single prefix check
        if (is_string($this->needles)) {
            return str_starts_with($validation->value, $this->needles);
        } // Multiple prefixes - check if any match
        else {
            foreach ($this->needles as $needle) {
                if (str_starts_with($validation->value, $needle)) {
                    return true;
                }
            }

            return false;
        }
    }
}
