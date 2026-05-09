<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use BackedEnum;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Exception\UnsupportedValidationTypeException;
use Switon\Validating\Validation;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_iterable;
use function is_object;
use function is_string;
use function method_exists;
use function ucfirst;

/**
 * Validation constraint attribute for type checks and conversions.
 *
 * Use when input should be validated against a target type and converted when supported.
 *
 * @see \Switon\Http\RequestBodyResolver::resolve()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Type extends AbstractConstraint
{
    /**
     * Create a new Type constraint.
     *
     * @param string $type Target type (string, int, float, bool, bit, array, object, mixed, iterable)
     * @param array $true Values considered as boolean true (for bool/bit conversion)
     * @param array $false Values considered as boolean false (for bool/bit conversion)
     * @param string|null $message Custom error message
     */
    public function __construct(
        public string  $type,
        public array   $true = [1, '1', 'true', 'on', 'yes'],
        public array   $false = [0, '0', 'false', 'off', 'no'],
        public ?string $message = null
    )
    {
        parent::__construct($this->message);
    }

    /**
     * Validate and convert value to the specified type.
     *
     * Dispatches to type-specific validation methods.
     *
     * @param Validation $validation Validation context
     * @return bool True if validation/conversion succeeded, false otherwise
     * @throws UnsupportedValidationTypeException If type is not supported
     */
    public function validate(Validation $validation): bool
    {
        if (enum_exists($this->type)) {
            return $this->validateEnum($validation);
        }

        // Build method name: validateInt, validateBool, etc.
        $method = 'validate' . ucfirst($this->type);

        if (method_exists($this, $method)) {
            return $this->$method($validation);
        }

        UnsupportedValidationTypeException::raise('Unsupported validation type "{type}"', ['type' => $this->type]);
    }

    /**
     * Validate and convert value to the target enum type.
     *
     * Backed enums accept the raw backing value; the value is replaced with the enum case.
     * Unit enums accept the case name as a string.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid or converted, false otherwise
     */
    protected function validateEnum(Validation $validation): bool
    {
        $enumClass = $this->type;
        $value = $validation->value;

        if ($value instanceof $enumClass) {
            return true;
        }

        if (is_subclass_of($enumClass, BackedEnum::class)) {
            foreach ($enumClass::cases() as $case) {
                if ($case->value === $value) {
                    $validation->value = $case;

                    return true;
                }
            }

            return false;
        }

        foreach ($enumClass::cases() as $case) {
            if ($case->name === $value) {
                $validation->value = $case;

                return true;
            }
        }

        return false;
    }

    /**
     * Validate/convert to boolean.
     *
     * Converts string representations to boolean values.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid/converted, false otherwise
     */
    protected function validateBool(Validation $validation): bool
    {
        if (!is_bool($validation->value)) {
            // Check if value is in the "true" list
            if (in_array($validation->value, $this->true, true)) {
                $validation->value = true;
            } // Check if value is in the "false" list
            elseif (in_array($validation->value, $this->false, true)) {
                $validation->value = false;
            } // Invalid boolean representation
            else {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate/convert to bit (0 or 1).
     *
     * Similar to bool but converts to integer 0/1 instead of boolean.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid/converted, false otherwise
     */
    protected function validateBit(Validation $validation): bool
    {
        if (!is_bool($validation->value)) {
            // Check if value is in the "true" list -> convert to 1
            if (in_array($validation->value, $this->true, true)) {
                $validation->value = 1;
            } // Check if value is in the "false" list -> convert to 0
            elseif (in_array($validation->value, $this->false, true)) {
                $validation->value = 0;
            } // Invalid bit representation
            else {
                return false;
            }
        } else {
            // Convert boolean to integer (true -> 1, false -> 0)
            $validation->value = (int)$validation->value;
        }

        return true;
    }

    /**
     * Validate/convert to float.
     *
     * Accepts integers, floats, and numeric strings.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid/converted, false otherwise
     */
    protected function validateFloat(Validation $validation): bool
    {
        // Already a numeric type
        if (is_int($validation->value) || is_float($validation->value)) {
            return true;
        } // Try to convert string to float
        elseif (is_string($validation->value)) {
            // Validate numeric format and convert
            if (filter_var($validation->value, FILTER_VALIDATE_FLOAT) !== false
                && preg_match('#^[+\-]?[\d.]+$#', $validation->value) === 1
            ) {
                $validation->value = (float)$validation->value;
            } else {
                return false;
            }
        } // Invalid type for float conversion
        else {
            return false;
        }

        return true;
    }

    /**
     * Validate/convert to integer.
     *
     * Accepts integers, booleans, and numeric strings.
     *
     * @param Validation $validation Validation context
     * @return bool True if valid/converted, false otherwise
     */
    protected function validateInt(Validation $validation): bool
    {
        // Already an integer
        if (is_int($validation->value)) {
            return true;
        } // Convert boolean to int (true -> 1, false -> 0)
        elseif (is_bool($validation->value)) {
            $validation->value = (int)$validation->value;
        } // Try to convert string to int
        elseif (is_string($validation->value)) {
            // Validate integer format (no decimals) and convert
            if (preg_match('#^[+\-]?\d+$#', $validation->value) === 1) {
                $validation->value = (int)$validation->value;
            } else {
                return false;
            }
        } // Invalid type for int conversion
        else {
            return false;
        }

        return true;
    }

    /**
     * Validate string type.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is a string, false otherwise
     */
    protected function validateString(Validation $validation): bool
    {
        return is_string($validation->value);
    }

    /**
     * Validate array type.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is an array, false otherwise
     */
    protected function validateArray(Validation $validation): bool
    {
        return is_array($validation->value);
    }

    /**
     * Validate mixed type (always passes).
     *
     * @param Validation $validation Validation context
     * @return bool Always returns true
     */
    /** @noinspection PhpUnusedParameterInspection */
    protected function validateMixed(Validation $validation): bool
    {
        // Mixed type accepts any value
        return true;
    }

    /**
     * Validate object type.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is an object, false otherwise
     */
    protected function validateObject(Validation $validation): bool
    {
        return is_object($validation->value);
    }

    /**
     * Validate iterable type.
     *
     * @param Validation $validation Validation context
     * @return bool True if value is iterable (array or Traversable), false otherwise
     */
    protected function validateIterable(Validation $validation): bool
    {
        return is_iterable($validation->value);
    }
}
