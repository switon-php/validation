<?php

declare(strict_types=1);

namespace Switon\Validating\Exception;

/**
 * Exception for invalid validation constraint sources.
 *
 * Thrown when a constraint expects an entity source but receives another value type.
 *
 * @see \Switon\Validating\Exception
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 */
class InvalidConstraintSourceException extends InvalidConstraintTargetException
{
}
