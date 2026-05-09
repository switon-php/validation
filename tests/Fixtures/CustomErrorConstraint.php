<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Fixtures;

use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

class CustomErrorConstraint extends AbstractConstraint
{
    public function validate(Validation $validation): bool
    {
        $validation->addError('Custom error for {field}');

        return false;
    }
}
