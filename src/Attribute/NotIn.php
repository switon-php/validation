<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function in_array;

/**
 * Validation constraint attribute for deny-list exclusion checks.
 *
 * Use when a field value must not appear in a predefined forbidden list.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class NotIn extends AbstractConstraint
{
    /**
     * Create a new NotIn constraint.
     *
     * @param array $values Blacklist of forbidden values
     * @param string|null $message Custom error message
     */
    public function __construct(public array $values, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate that value is NOT in the blacklist.
     *
     * Uses strict comparison (===) to ensure type safety.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is NOT in the blacklist, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Strict comparison ensures type safety (e.g., 1 !== "1")
        return !in_array($validation->value, $this->values, true);
    }
}
