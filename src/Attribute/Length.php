<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function mb_strlen;

/**
 * Validation constraint attribute for string length rules.
 *
 * Use when a field must satisfy exact, minimum, or range-based character length limits.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Length extends AbstractConstraint
{
    /**
     * Create a new Length constraint.
     *
     * @param int $min Minimum length (or exact length if max is null)
     * @param int|null $max Maximum length (null for exact length validation)
     * @param string|null $message Custom error message
     */
    public function __construct(public int $min, public ?int $max = null, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate string length.
     *
     * This method:
     * 1. Counts characters using mb_strlen() (Unicode-safe)
     * 2. If max is null: validates exact length (length === min)
     * 3. If max is set: validates range (min <= length <= max)
     *
     * @param Validation $validation Validation context
     * @return bool True if length is valid, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Use multibyte-safe character counting
        $length = mb_strlen($validation->value);

        // If max is null, validate exact length
        if ($this->max === null) {
            return $length === $this->min;
        } // Otherwise, validate range (inclusive)
        else {
            return $length >= $this->min && $length <= $this->max;
        }
    }
}
