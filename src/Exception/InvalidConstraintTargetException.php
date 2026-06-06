<?php

declare(strict_types=1);

namespace Switon\Validating\Exception;

use Switon\Validating\Exception as BaseException;

/**
 * Base exception for invalid validation constraint source and target mismatches.
 *
 * @see \Switon\Validating\Exception
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\AbstractConstraint
 */
class InvalidConstraintTargetException extends BaseException
{
}
