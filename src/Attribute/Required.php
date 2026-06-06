<?php

declare(strict_types=1);

namespace Switon\Validating\Attribute;

use Attribute;
use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

/**
 * Validation constraint attribute for required fields.
 *
 * Use when a field must be present and not null in the validation input.
 *
 * @see \Switon\Http\RequestBodyResolver::resolve()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Required extends AbstractConstraint
{
    /**
     * Validate that the current value is present.
     *
     * `isset()` semantics mean explicit `null` fails while `0`, `false`, and empty string pass.
     */
    public function validate(Validation $validation): bool
    {
        return isset($validation->value);
    }
}
