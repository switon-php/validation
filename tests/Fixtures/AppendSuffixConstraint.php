<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Fixtures;

use Switon\Validating\AbstractConstraint;
use Switon\Validating\Validation;

class AppendSuffixConstraint extends AbstractConstraint
{
    public function __construct(public string $suffix, public ?string $message = null)
    {
        parent::__construct($message);
    }

    public function validate(Validation $validation): bool
    {
        if (!is_string($validation->value)) {
            return false;
        }

        $validation->value .= $this->suffix;

        return true;
    }
}
