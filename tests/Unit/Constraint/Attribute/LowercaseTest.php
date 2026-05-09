<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Lowercase;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Lowercase constraint.
 *
 * Tests lowercase string validation and sanitization.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class LowercaseTest extends TestCase
{
    /**
     * Test Lowercase sanitizes to lowercase when sanitize is true.
     */
    public function testLowercaseSanitizesToLowercaseWhenSanitizeIsTrue(): void
    {
        $constraint = new Lowercase(true);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'TEST VALUE';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Lowercase passes when value is already lowercase (sanitize mode).
     */
    public function testLowercasePassesWhenValueIsAlreadyLowercaseSanitize(): void
    {
        $constraint = new Lowercase(true);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'test value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Lowercase validates without sanitization when sanitize is false.
     */
    public function testLowercaseValidatesWithoutSanitizationWhenSanitizeIsFalse(): void
    {
        $constraint = new Lowercase(false);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'test value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Lowercase fails validation when sanitize is false and value is not lowercase.
     */
    public function testLowercaseFailsValidationWhenSanitizeIsFalseAndValueIsNotLowercase(): void
    {
        $constraint = new Lowercase(false);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'TEST VALUE';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Lowercase throws exception when fails.
     */
    public function testLowercaseThrowsExceptionWhenFails(): void
    {
        $constraint = new Lowercase(false);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'UPPERCASE', [$constraint]);
    }
}

