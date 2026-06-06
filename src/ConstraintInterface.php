<?php

declare(strict_types=1);

namespace Switon\Validating;

/**
 * Defines the contract for validation constraints.
 *
 * Use when creating built-in or custom attributes that validate values through a shared
 * <code>Validation</code> context.
 *
 * Road-signs:
 * - validate(): constraint execution hook
 * - getMessage(): default template key or inline message
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 * @see \Switon\Validating\Validator
 * @see \Switon\Validating\Attribute\ArrayOf Array element typing for typed-input binding
 * @see \Switon\Validating\Exception\ConstraintViolationException
 * @see \Switon\Http\RequestBodyResolver::resolve() Reads constraint attributes from typed-input properties and calls validate()
 */
interface ConstraintInterface
{
    /**
     * Validate the current field value inside one validation session.
     *
     * @param Validation $validation Validation context containing field, value, and source
     *
     * @return bool `true` if validation passes, `false` otherwise
     */
    public function validate(Validation $validation): bool;

    /**
     * Return the default message template or message key for this constraint.
     *
     * @return string Error message template or constraint class name
     */
    public function getMessage(): string;
}
