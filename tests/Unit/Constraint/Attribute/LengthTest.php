<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Length;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class LengthTest extends TestCase
{
    public function testLengthPassesWhenLengthEqualsMinimum(): void
    {
        $constraint = new Length(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '12345';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testLengthPassesWhenLengthIsWithinRange(): void
    {
        $constraint = new Length(4, 8);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '12345';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testLengthPassesWhenLengthEqualsMinimumInRange(): void
    {
        $constraint = new Length(4, 8);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '1234';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testLengthPassesWhenLengthEqualsMaximumInRange(): void
    {
        $constraint = new Length(4, 8);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '12345678';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testLengthFailsWhenLengthIsLessThanMinimum(): void
    {
        $constraint = new Length(4, 8);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '123';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testLengthFailsWhenLengthIsGreaterThanMaximum(): void
    {
        $constraint = new Length(4, 8);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '123456789';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testLengthFailsWhenLengthDoesNotMatchExactLength(): void
    {
        $constraint = new Length(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '1234';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testLengthWorksWithMultibyteStrings(): void
    {
        // Arrange - Chinese characters: 4 chars, 12 bytes
        $constraint = new Length(4);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '测试数据';

        // Act
        $result = $constraint->validate($validation);

        // Assert - mb_strlen counts characters, not bytes
        $this->assertTrue($result, 'Length should count characters, not bytes');
    }

    public function testLengthMultibyteRangeValidation(): void
    {
        // Arrange - 2 characters, range 1-3
        $constraint = new Length(1, 3);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'value';
        $validation->value = '你好';

        // Act
        $result = $constraint->validate($validation);

        // Assert
        $this->assertTrue($result, 'Multibyte string within range should pass');
    }

    public function testLengthThrowsExceptionWhenFails(): void
    {
        $constraint = new Length(4, 8);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('value', '123', [$constraint]);
    }
}
