<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\MaxLength;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for MaxLength constraint.
 *
 * Tests maximum string length validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class MaxLengthTest extends TestCase
{
    /**
     * Test MaxLength passes when length equals maximum.
     */
    public function testMaxLengthPassesWhenLengthEqualsMaximum(): void
    {
        $constraint = new MaxLength(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '12345';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test MaxLength passes when length is less than maximum.
     */
    public function testMaxLengthPassesWhenLengthIsLessThanMaximum(): void
    {
        $constraint = new MaxLength(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '1234';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test MaxLength fails when length is greater than maximum.
     */
    public function testMaxLengthFailsWhenLengthIsGreaterThanMaximum(): void
    {
        $constraint = new MaxLength(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123456';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test MaxLength works with multibyte strings.
     */
    public function testMaxLengthWorksWithMultibyteStrings(): void
    {
        $constraint = new MaxLength(3);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '测试'; // 2 characters, 6 bytes

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test MaxLength throws exception when fails.
     */
    public function testMaxLengthThrowsExceptionWhenFails(): void
    {
        $constraint = new MaxLength(3);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', '1234', [$constraint]);
    }
}
