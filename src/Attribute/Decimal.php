<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function explode;
use function str_contains;
use function strlen;

/**
 * Validation constraint attribute for decimal precision and scale limits.
 *
 * Use when numeric input must fit a DECIMAL(M,D)-style range.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Decimal extends AbstractConstraint
{
    /**
     * Create a new Decimal constraint.
     *
     * @param int $M Maximum total digits (precision), default 10
     * @param int $D Maximum decimal places (scale), default 0
     * @param string|null $message Custom error message
     */
    public function __construct(public int $M = 10, public int $D = 0, public ?string $message = null)
    {
        parent::__construct($message);
    }

    /**
     * Validate decimal precision and scale.
     *
     * This method:
     * 1. Checks if value is numeric
     * 2. Validates decimal places don't exceed D
     * 3. Validates total digits don't exceed M
     * 4. Handles negative numbers correctly
     *
     * @param Validation $validation Validation context
     * @return bool True if value fits within M,D constraints, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        // Must be numeric
        if (!is_numeric($validation->value)) {
            return false;
        }

        $value = (string)$validation->value;

        // Handle negative sign (doesn't count toward digit limit)
        $value = ltrim($value, '-+');

        $integerPart = '';
        $decimalPart = '';

        // Split into integer and decimal parts
        if (str_contains($value, '.')) {
            [$integerPart, $decimalPart] = explode('.', $value, 2);

            // Check if decimal places exceed D
            if (strlen($decimalPart) > $this->D) {
                return false;
            }
        } else {
            $integerPart = $value;
        }

        // M represents total number of digits (not including decimal point)
        // Check: integer_digits + decimal_digits <= M
        $totalDigits = strlen($integerPart) + strlen($decimalPart);

        if ($totalDigits > $this->M) {
            return false;
        }

        return true;
    }
}
