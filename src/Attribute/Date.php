<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use ReflectionProperty;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;
use function date;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function property_exists;
use function str_contains;
use function strtotime;

/**
 * Validation constraint attribute for date parsing and normalization.
 *
 * Use when a field must be a valid date and should be normalized to the declared property type.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Date extends AbstractConstraint
{
    protected const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param string $format String output format used when the target type is <code>string</code>
     * @param string|null $message Custom error message
     */
    public function __construct(public string $format = self::DEFAULT_FORMAT, public ?string $message = null)
    {
        parent::__construct($this->message);
    }

    /**
     * Validate and convert date value.
     *
     * This method:
     * 1. Validates that the value is a valid date (timestamp or parseable string)
     * 2. Converts the value to match the declared target type when available
     * 3. Modifies $validation->value with the converted value
     *
     * @param Validation $validation Validation context
     * @return bool True if value is a valid date, false otherwise
     */
    public function validate(Validation $validation): bool
    {
        $value = $validation->value;

        // Convert value to Unix timestamp
        // - If numeric, cast to int
        // - If string, parse using strtotime()
        $ts = is_numeric($value) ? (int)$value : (is_string($value) ? strtotime($value) : false);

        // Invalid date format
        if ($ts === false) {
            return false;
        }

        $targetType = $validation->targetType ?? $this->resolveTargetType($validation);

        // If property is int type and value is string, convert to timestamp
        if ($targetType === 'int' && !is_int($value)) {
            $validation->value = $ts;
        }

        // If property is string type, normalize to configured string format
        if ($targetType === 'string' && (!is_string($value) || $this->format !== self::DEFAULT_FORMAT)) {
            $validation->value = date($this->format, $ts);
        }

        return true;
    }

    /**
     * Resolves the declared target type from the validation context when possible.
     */
    protected function resolveTargetType(Validation $validation): ?string
    {
        if (!is_object($validation->source)
            || str_contains($validation->field, '.')
            || !property_exists($validation->source, $validation->field)
        ) {
            return null;
        }

        $rProperty = new ReflectionProperty($validation->source, $validation->field);
        return $rProperty->getType()?->getName();
    }
}
