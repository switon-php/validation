<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Alnum;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Alnum constraint.
 *
 * Tests alphanumeric string validation (letters and digits only).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class AlnumTest extends TestCase
{
    /**
     * Test Alnum passes with alphanumeric string.
     */
    public function testAlnumPassesWithAlphanumericString(): void
    {
        $constraint = new Alnum();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Alnum passes with only letters.
     */
    public function testAlnumPassesWithOnlyLetters(): void
    {
        $constraint = new Alnum();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Alnum passes with only digits.
     */
    public function testAlnumPassesWithOnlyDigits(): void
    {
        $constraint = new Alnum();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Alnum fails with special characters.
     */
    public function testAlnumFailsWithSpecialCharacters(): void
    {
        $constraint = new Alnum();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc-123';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Alnum fails with spaces.
     */
    public function testAlnumFailsWithSpaces(): void
    {
        $constraint = new Alnum();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc 123';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Alnum fails with empty string.
     */
    public function testAlnumFailsWithEmptyString(): void
    {
        $constraint = new Alnum();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result, 'Empty string should fail alnum validation');
    }

    /**
     * Test Alnum throws exception when fails.
     */
    public function testAlnumThrowsExceptionWhenFails(): void
    {
        $constraint = new Alnum();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'abc-123', [$constraint]);
    }
}

