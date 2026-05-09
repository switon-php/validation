<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function trim;

/**
 * Validation constraint attribute for edge-character trimming.
 *
 * Use when string input should be normalized by trimming whitespace or configured characters.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Trim extends AbstractConstraint
{
    /**
     * Create a new Trim constraint.
     *
     * @param string $characters Characters to trim (default: whitespace)
     * @param string|null $message Custom error message
     */
    public function __construct(public string $characters = " \n\r\t\v\x00", public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Trim characters from the value.
     *
     * This constraint always returns true (never fails) as it's a sanitizer.
     * Modifies $validation->value with the trimmed result.
     *
     * @param Validation $validation Validation context
     * @return bool Always returns true
     */
    public function validate(Validation $validation): bool
    {
        // Trim specified characters from both ends
        $validation->value = trim($validation->value, $this->characters);

        // Always passes validation (this is a sanitizer, not a validator)
        return true;
    }
}
