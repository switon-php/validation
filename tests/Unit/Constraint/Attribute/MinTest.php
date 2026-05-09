<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Min;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class MinTest extends TestCase
{
    public function testMinPassesWhenValueEqualsMinimum(): void
    {
        $constraint = new Min(10);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 10;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testMinPassesWhenValueIsGreaterThanMinimum(): void
    {
        $constraint = new Min(10);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 15;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testMinFailsWhenValueIsLessThanMinimum(): void
    {
        $constraint = new Min(10);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 5;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testMinWorksWithFloatValues(): void
    {
        $constraint = new Min(10.5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 10.6;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testMinThrowsExceptionWhenFails(): void
    {
        $constraint = new Min(10);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('value', 5, [$constraint]);
    }
}
