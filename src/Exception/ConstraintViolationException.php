<?php

declare(strict_types=1);

namespace Switon\Validating\Exception;

use Switon\Validating\Exception as BaseException;

/**
 * Exception for single validation constraint violations.
 *
 * Thrown when a constraint marks the current value as invalid during validation.
 *
 * Use when validation should abort around one immediate constraint failure instead of
 * waiting for aggregated session finalization.
 *
 * @see \Switon\Validating\Exception
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\AbstractConstraint
 */
class ConstraintViolationException extends BaseException
{
}
