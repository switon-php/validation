<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function is_string;
use function str_ends_with;

/**
 * Validation constraint attribute for suffix matching.
 *
 * Use when a string field must end with one or more required suffixes.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class EndsWith extends AbstractConstraint
{
    /**
     * Create a new EndsWith constraint.
     *
     * @param array|string $needles Single suffix or array of suffixes
     * @param string|null $message Custom error message
     */
    public function __construct(public array|string $needles, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate that value ends with one of the specified suffixes.
     *
     * @param Validation $validation Validation context
     * @return bool True if value ends with any of the needles, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Single suffix check
        if (is_string($this->needles)) {
            return str_ends_with($validation->value, $this->needles);
        } // Multiple suffixes - check if any match
        else {
            foreach ($this->needles as $needle) {
                if (str_ends_with($validation->value, $needle)) {
                    return true;
                }
            }

            return false;
        }
    }
}
