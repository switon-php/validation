<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Max;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class MaxTest extends TestCase
{
    public function testMaxPassesWhenValueEqualsMaximum(): void
    {
        $constraint = new Max(10);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 10;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testMaxPassesWhenValueIsLessThanMaximum(): void
    {
        $constraint = new Max(10);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 5;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testMaxFailsWhenValueIsGreaterThanMaximum(): void
    {
        $constraint = new Max(10);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 15;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testMaxWorksWithFloatValues(): void
    {
        // Arrange
        $constraint = new Max(10.5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = 10.4;

        // Act
        $result = $constraint->validate($validation);

        // Assert
        $this->assertTrue($result);
    }

    public function testMaxWorksWithNegativeValues(): void
    {
        // Arrange
        $constraint = new Max(-5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = -10;

        // Act
        $result = $constraint->validate($validation);

        // Assert
        $this->assertTrue($result, 'Negative value less than negative max should pass');
    }

    public function testMaxThrowsExceptionWhenFails(): void
    {
        $constraint = new Max(10);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('value', 15, [$constraint]);
    }
}
