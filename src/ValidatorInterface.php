<?php

declare(strict_types=1);

namespace Switon\Validating;

/**
 * Defines the validation contract for values and field sets.
 *
 * Use when application or framework code must validate single fields, full payloads, or manual validation sessions.
 *
 * Road-signs:
 * - validateValue() / validateValues(): caller-facing entrypoints
 * - beginValidate() / endValidate(): framework-internal session boundary
 * - formatMessage(): message-template rendering
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
     * @param list<ConstraintInterface> $constraints Constraint instances
     *
     * @return mixed Validated value, or null if validation fails
     */
    public function validateValue(string $field, mixed $value, array $constraints): mixed;

    /**
     * Validate multiple values against constraints.
     *
     * @param array<string, mixed> $source Source data (field => value)
     * @param array<string, ConstraintInterface|list<ConstraintInterface>> $constraints Constraints (field => constraint list)
     *
     * @return array<string, mixed> Validated values (field => validated value)
     */
    public function validateValues(array $source, array $constraints): array;

    /**
     * Begin a validation session.
     *
     * Intended for framework-internal use (for example request-body binding and entity persistence).
     * Application code should prefer `validateValue()` or `validateValues()`.
     * Callers MUST pair this with `endValidate()` so collected errors are finalized.
     *
     * @param array<string, mixed>|object $source Source data
     *
     * @return Validation Validation object for manual validation
     */
    public function beginValidate(array|object $source): Validation;

    /**
     * End a validation session.
     *
     * Intended for framework-internal use. Call after `beginValidate()` to finalize
     * validation and throw if the session collected errors.
     *
     * @param Validation $validation Validation object
     */
    public function endValidate(Validation $validation): void;

    /**
     * Format validation error message.
     *
     * Intended for framework-internal use from the validation session/context layer.
     *
     * @param string $message Constraint FQCN (template key) or custom message template
     * @param array<string, mixed> $placeholders Placeholder values for message formatting
     *
     * @return string Formatted message
     */
    public function formatMessage(string $message, array $placeholders = []): string;
}
