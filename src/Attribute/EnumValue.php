<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use BackedEnum;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Exception\InvalidConstraintTargetException;
use Switon\Validating\Validation;
use UnitEnum;

/**
 * Validation constraint attribute for enum-backed value membership.
 *
 * Use when a field value must match one of the cases of the target enum (same family as
 * {@see \Switon\Validating\Attribute\ConstantValue} and dict-backed <code>DictValue</code>).
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 * @see \Switon\Validating\Validation::validate()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class EnumValue extends AbstractConstraint
{
    /**
     * Create a new EnumValue constraint.
     *
     * @param class-string<UnitEnum> $class Target enum class
     * @param string|null $message Custom error message
     */
    public function __construct(
        public string  $class,
        public ?string $message = null,
    )
    {
        parent::__construct(message: $message);
    }

    /**
     * Validate that value matches one of the enum cases.
     *
     * Backed enums accept the raw backing value or the enum case instance.
     * Unit enums accept the case name or the enum case instance.
     *
     * @param Validation $validation Validation context
     * @return bool True if value matches an enum case, false otherwise
     * @throws InvalidConstraintTargetException When the configured class is not a valid enum
     */
    public function validate(Validation $validation): bool
    {
        $enumClass = $this->resolveEnumClass();
        $value = $validation->value;

        if ($value instanceof $enumClass) {
            return true;
        }

        if (is_subclass_of($enumClass, BackedEnum::class)) {
            foreach ($enumClass::cases() as $case) {
                if ($case->value === $value) {
                    return true;
                }
            }

            return false;
        }

        foreach ($enumClass::cases() as $case) {
            if ($case->name === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve and validate the configured enum class.
     *
     * @return class-string<UnitEnum>
     * @throws InvalidConstraintTargetException When the configured class is not a valid enum
     */
    protected function resolveEnumClass(): string
    {
        if (!enum_exists($this->class)) {
            InvalidConstraintTargetException::raise(
                'Constraint #{constraint} references invalid enum class {class}',
                ['constraint' => static::class, 'class' => $this->class]
            );
        }

        return $this->class;
    }
}
