<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Range;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class RangeTest extends TestCase
{
    public function testRangePassesWhenValueEqualsMinimum(): void
    {
        $constraint = new Range(10, 20);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 10;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testRangePassesWhenValueEqualsMaximum(): void
    {
        $constraint = new Range(10, 20);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 20;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testRangePassesWhenValueIsWithinRange(): void
    {
        $constraint = new Range(10, 20);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 15;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testRangeFailsWhenValueIsLessThanMinimum(): void
    {
        $constraint = new Range(10, 20);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 5;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testRangeFailsWhenValueIsGreaterThanMaximum(): void
    {
        $constraint = new Range(10, 20);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 25;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testRangeThrowsExceptionWhenFails(): void
    {
        $constraint = new Range(10, 20);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('value', 5, [$constraint]);
    }
}
