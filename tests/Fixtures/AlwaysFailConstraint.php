<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Fixtures;

use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

class AlwaysFailConstraint extends AbstractConstraint
{
    public function validate(Validation $validation): bool
    {
        return false;
    }
}
