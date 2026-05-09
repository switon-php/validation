<?php

declare(strict_types=1);

namespace Switon\Validating\Exception;

use Switon\Validating\Exception as BaseException;

/**
 * Exception for missing validation locale templates.
 *
 * Thrown when no message template file can be resolved for the requested locale.
 *
 * @see \Switon\Validating\Exception
 * @see \Switon\Validating\Validator
 */
class LocaleTemplateNotFoundException extends BaseException
{
}
