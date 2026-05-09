<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\MinLength;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for MinLength constraint.
 *
 * Tests minimum string length validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class MinLengthTest extends TestCase
{
    /**
     * Test MinLength passes when length equals minimum.
     */
    public function testMinLengthPassesWhenLengthEqualsMinimum(): void
    {
        $constraint = new MinLength(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '12345';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test MinLength passes when length is greater than minimum.
     */
    public function testMinLengthPassesWhenLengthIsGreaterThanMinimum(): void
    {
        $constraint = new MinLength(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123456';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test MinLength fails when length is less than minimum.
     */
    public function testMinLengthFailsWhenLengthIsLessThanMinimum(): void
    {
        $constraint = new MinLength(5);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '1234';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test MinLength works with multibyte strings.
     */
    public function testMinLengthWorksWithMultibyteStrings(): void
    {
        $constraint = new MinLength(3);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '测试测试'; // 4 characters

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test MinLength throws exception when fails.
     */
    public function testMinLengthThrowsExceptionWhenFails(): void
    {
        $constraint = new MinLength(5);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', '1234', [$constraint]);
    }
}
