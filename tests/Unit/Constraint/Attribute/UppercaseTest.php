<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Uppercase;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Uppercase constraint.
 *
 * Tests uppercase string sanitization and validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class UppercaseTest extends TestCase
{
    /**
     * Test Uppercase sanitizes to uppercase.
     */
    public function testUppercaseSanitizesToUppercase(): void
    {
        $constraint = new Uppercase();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'test value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('TEST VALUE', $validation->value);
    }

    /**
     * Test Uppercase passes when value is already uppercase.
     */
    public function testUppercasePassesWhenValueIsAlreadyUppercase(): void
    {
        $constraint = new Uppercase();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'TEST VALUE';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('TEST VALUE', $validation->value);
    }

    /**
     * Test Uppercase handles mixed case.
     */
    public function testUppercaseHandlesMixedCase(): void
    {
        $constraint = new Uppercase();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'Test Value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('TEST VALUE', $validation->value);
    }

    /**
     * Test Uppercase validate mode passes when value is already uppercase.
     */
    public function testUppercaseValidateModePassesWhenAlreadyUppercase(): void
    {
        $constraint = new Uppercase(sanitize: false);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'TEST VALUE';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('TEST VALUE', $validation->value);
    }

    /**
     * Test Uppercase validate mode fails when value is not uppercase.
     */
    public function testUppercaseValidateModeFailsWhenNotUppercase(): void
    {
        $constraint = new Uppercase(sanitize: false);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'Test Value';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }
}

