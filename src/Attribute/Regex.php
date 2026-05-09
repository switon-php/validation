<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function preg_match;

/**
 * Validation constraint attribute for custom regular-expression checks.
 *
 * Use when built-in constraints are not enough and a field must match a specific pattern.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Regex extends AbstractConstraint
{
    /**
     * Create a new Regex constraint.
     *
     * @param string $pattern Regular expression pattern (with delimiters)
     * @param string|null $message Custom error message
     */
    public function __construct(public string $pattern, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate value against regex pattern.
     *
     * @param Validation $validation Validation context
     * @return bool True if value matches pattern, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Match value against the provided regex pattern
        return preg_match($this->pattern, $validation->value) === 1;
    }
}
