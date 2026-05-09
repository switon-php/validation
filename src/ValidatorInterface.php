<?php

declare(strict_types=1);

namespace Switon\Validating;

/**
 * Defines the validation contract for values and field sets.
 *
 * Use when application or framework code must validate single fields, full payloads, or manual validation sessions.
 *
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\ValidatorInterface::beginValidate()
 * @see \Switon\Validating\ValidatorInterface::endValidate()
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 * @see \Switon\Validating\ConstraintInterface
 * @see \Switon\Validating\Attribute\ArrayOf Array element typing for typed-input binding
 * @see \Switon\Validating\Exception\ValidateFailedException
 * @see \Switon\Validating\Exception\ValidateFailedException::raiseForValidationFailed()
 * @see \Switon\Http\RequestBodyResolver::resolve() Main consumer: typed-input population + attribute-driven validation
 * @see \Switon\Orm\AbstractEntityManager Entity save validation
 */
interface ValidatorInterface
{
    /**
     * Validate a single value against constraints.
     *
     * @param string $field Field name
     * @param mixed $value Value to validate
     * @param array $constraints Array of constraint instances
     * @return mixed Validated value, or null if validation fails
     */
    public function validateValue(string $field, mixed $value, array $constraints): mixed;

    /**
     * Validate multiple values against constraints.
     *
     * @param array $source Source data (field => value)
     * @param array $constraints Constraints (field => constraint array)
     * @return array Validated values (field => validated value)
     */
    public function validateValues(array $source, array $constraints): array;

    /**
     * Begin a validation session.
     *
     * Intended for framework-internal use (e.g., RequestBody, EntityManager).
     * Application code should use `validateValue()` or `validateValues()` instead.
     * Caller MUST call `endValidate()` after validation to ensure errors are thrown.
     *
     * @param array|object $source Source data
     * @return Validation Validation object for manual validation
     */
    public function beginValidate(array|object $source): Validation;

    /**
     * End a validation session.
     *
     * Intended for framework-internal use. Must be called after `beginValidate()`
     * to finalize validation and throw on errors.
     *
     * @param Validation $validation Validation object
     * @return void
     */
    public function endValidate(Validation $validation): void;

    /**
     * Format validation error message.
     *
     * Intended for framework-internal use (called by Validation context).
     *
     * @param string $message Constraint FQCN (template key) or custom message template
     * @param array $placeholders Placeholder values for message formatting
     * @return string Formatted message
     */
    public function formatMessage(string $message, array $placeholders = []): string;
}
