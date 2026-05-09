<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;

/**
 * Declares array element type metadata for one property.
 *
 * Commonly used by typed-input binders and runtime validators for nested arrays.
 *
 * @see \Switon\Binding\InputBinder
 * @see \Switon\Http\RequestBodyResolver
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayOf
{
    /** @param string $type Element type (FQCN or scalar name) */
    public function __construct(
        public readonly string $type,
        public readonly ?int $minItems = null,
        public readonly ?int $maxItems = null,
    ) {
    }
}
