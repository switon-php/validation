<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Digit;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Digit constraint.
 *
 * Tests digit-only string validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class DigitTest extends TestCase
{
    /**
     * Test Digit passes with digit string.
     */
    public function testDigitPassesWithDigitString(): void
    {
        $constraint = new Digit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Digit fails with letters.
     */
    public function testDigitFailsWithLetters(): void
    {
        $constraint = new Digit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123abc';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Digit fails with special characters.
     */
    public function testDigitFailsWithSpecialCharacters(): void
    {
        $constraint = new Digit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123-456';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Digit fails with empty string.
     */
    public function testDigitFailsWithEmptyString(): void
    {
        $constraint = new Digit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result, 'Empty string should fail digit validation');
    }

    /**
     * Test Digit throws exception when fails.
     */
    public function testDigitThrowsExceptionWhenFails(): void
    {
        $constraint = new Digit();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', '123abc', [$constraint]);
    }
}

