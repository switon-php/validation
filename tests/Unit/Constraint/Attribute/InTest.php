<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\In;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class InTest extends TestCase
{
    public function testInPassesWhenValueIsInArray(): void
    {
        $constraint = new In(['red', 'green', 'blue']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'color';
        $validation->value = 'red';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testInFailsWhenValueIsNotInArray(): void
    {
        $constraint = new In(['red', 'green', 'blue']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'color';
        $validation->value = 'yellow';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testInWorksWithIntegerValues(): void
    {
        $constraint = new In([1, 2, 3]);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 2;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testInUsesStrictComparison(): void
    {
        $constraint = new In([1, 2, 3]);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '1';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testInThrowsExceptionWhenFails(): void
    {
        $constraint = new In(['red', 'green', 'blue']);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('color', 'yellow', [$constraint]);
    }
}
