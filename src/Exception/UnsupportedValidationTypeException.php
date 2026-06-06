<?php

declare(strict_types=1);

namespace Switon\Validating\Exception;

use Switon\Validating\Exception as BaseException;

/**
 * Exception for unsupported <code>Type</code> constraint targets.
 *
 * Thrown when a configured validation type has no corresponding checker in the type constraint.
 *
 * @see \Switon\Validating\Exception
 * @see \Switon\Validating\Attribute\Type
 * @see \Switon\Validating\Validator
 */
class UnsupportedValidationTypeException extends BaseException
{
}
