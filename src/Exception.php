<?php

declare(strict_types=1);

namespace Switon\Validating;

/**
 * Base exception for the Validating component.
 *
 * Use when validation internals need a shared component-level exception root.
 *
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\Exception\ValidateFailedException
 * @see \Switon\Validating\Exception\ConstraintViolationException
 * @see \Switon\Core\Exception
 */
class Exception extends \Switon\Core\Exception
{
}
