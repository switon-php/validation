<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function in_array;

/**
 * Validation constraint attribute for allow-list membership checks.
 *
 * Use when a field value must be one of the predefined allowed options.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class In extends AbstractConstraint
{
    /**
     * Create a new In constraint.
     *
     * @param array $values Whitelist of allowed values
     * @param string|null $message Custom error message
     */
    public function __construct(public array $values, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate that value is in the whitelist.
     *
     * Uses strict comparison (===) to ensure type safety.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is in the whitelist, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Strict comparison ensures type safety (e.g., 1 !== "1")
        return in_array($validation->value, $this->values, true);
    }
}
