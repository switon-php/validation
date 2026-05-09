<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Attribute\PropertyNormalizerInterface;
use Switon\Validating\Exception\InvalidConstraintSourceException;
use Switon\Validating\Exception\InvalidConstraintTargetException;
use Switon\Validating\Validation;
use function get_debug_type;
use function is_object;
use function str_starts_with;
use function strtoupper;

/**
 * Validation attribute for constant-backed value membership.
 *
 * Use when a field value must match one of the target class constants resolved by field prefix.
 *
 * @see \Switon\Validating\AbstractConstraint
 * @see \Switon\Validating\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ConstantValue extends AbstractConstraint implements PropertyNormalizerInterface
{
    /**
     * @param string|null $prefix Constant prefix name (defaults to field name)
     * @param string|null $message Custom error message
     * @param class-string|null $class Constant class (defaults to current source object)
     */
    public function __construct(
        public ?string $prefix = null,
        public ?string $message = null,
        public ?string $class = null,
    )
    {
        parent::__construct(message: $message);
    }

    public function validate(Validation $validation): bool
    {
        $rClass = $this->resolveConstantClass($validation);
        $prefix = strtoupper($this->prefix ?? $validation->field) . '_';
        $matched = false;

        foreach ($rClass->getConstants() as $name => $value) {
            if (!str_starts_with($name, $prefix)) {
                continue;
            }

            $matched = true;
            if ($value === $validation->value) {
                return true;
            }
        }

        if (!$matched) {
            InvalidConstraintTargetException::raise(
                'Constraint #{constraint} could not find any constants in {class} with prefix {prefix}',
                ['constraint' => static::class, 'class' => $rClass->getName(), 'prefix' => $prefix]
            );
        }

        return false;
    }

    public function normalizeInput(ReflectionProperty $property, mixed $value): mixed
    {
        return $value;
    }

    /**
     * @return ReflectionClass<object>
     */
    protected function resolveConstantClass(Validation $validation): ReflectionClass
    {
        $class = $this->class;

        if ($class === null) {
            if (!is_object($validation->source)) {
                InvalidConstraintSourceException::raise(
                    'Constraint #{constraint} requires an object source or explicit class, got {type}',
                    ['constraint' => static::class, 'type' => get_debug_type($validation->source)]
                );
            }

            $class = $validation->source::class;
        }

        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            InvalidConstraintTargetException::raise(
                'Constraint #{constraint} references invalid constant class {class}',
                ['constraint' => static::class, 'class' => $class],
                previous: $exception,
            );
        }
    }
}
