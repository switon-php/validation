<?php

declare(strict_types=1);

namespace Switon\Validating\Exception;

use Exception;
use Switon\Core\Json;
use Switon\Validating\Exception as BaseException;

/**
 * Exception for aggregated validation failures.
 *
 * Thrown when one or more fields fail validation and a 400 response payload must carry all errors.
 *
 * @see \Switon\Validating\ValidatorInterface::endValidate()
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::getErrors()
 * @see \Switon\Validating\Exception\ValidateFailedException::raiseForValidationFailed()
 */
class ValidateFailedException extends BaseException
{
    /** @var array<string, string> Validation errors keyed by field name */
    protected array $errors;

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    /**
     * Raises exception when validation fails.
     *
     * @param array<string, string> $errors Validation errors
     * @param int $code Error code
     * @param Exception|null $previous Previous exception
     * @return never
     */
    public static function raiseForValidationFailed(array $errors, int $code = 0, ?Exception $previous = null): never
    {
        $exception = new static(Json::stringify($errors), [], $code, $previous);
        $exception->errors = $errors;
        $exception->json = [
            'code' => -1,
            'msg' => Json::stringify($errors, JSON_PRETTY_PRINT),
            'data' => ['validator.errors' => $errors]
        ];
        throw $exception;
    }
}
