<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Fixtures;

use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

class UppercaseConstraint extends AbstractConstraint
{
    public function validate(Validation $validation): bool
    {
        if (!is_string($validation->value)) {
            return false;
        }

        $validation->value = strtoupper($validation->value);

        return true;
    }
}
