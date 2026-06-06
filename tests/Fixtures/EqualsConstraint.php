<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Fixtures;

use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

class EqualsConstraint extends AbstractConstraint
{
    public function __construct(public mixed $expected, public ?string $message = null)
    {
        parent::__construct($message);
    }

    public function validate(Validation $validation): bool
    {
        return $validation->value === $this->expected;
    }
}
