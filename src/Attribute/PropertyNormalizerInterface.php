<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use ReflectionProperty;

/**
 * Defines property-value normalization for object input binding.
 *
 * Use when an attribute should rewrite one raw input value before the target object is hydrated.
 */
interface PropertyNormalizerInterface
{
    /**
     * Normalize one raw input value for the target property.
     *
     * @param ReflectionProperty $property Target property metadata
     * @param mixed $value Raw input value
     * @return mixed Normalized value
     */
    public function normalizeInput(ReflectionProperty $property, mixed $value): mixed;
}
