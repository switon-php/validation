<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Xdigit;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Xdigit constraint.
 *
 * Tests hexadecimal digit string validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class XdigitTest extends TestCase
{
    /**
     * Test Xdigit passes with hexadecimal string.
     */
    public function testXdigitPassesWithHexadecimalString(): void
    {
        $constraint = new Xdigit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Xdigit passes with uppercase hexadecimal.
     */
    public function testXdigitPassesWithUppercaseHexadecimal(): void
    {
        $constraint = new Xdigit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'ABC123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Xdigit fails with invalid hexadecimal characters.
     */
    public function testXdigitFailsWithInvalidHexadecimalCharacters(): void
    {
        $constraint = new Xdigit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc123g'; // 'g' is not a valid hex digit

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Xdigit fails with empty string.
     */
    public function testXdigitFailsWithEmptyString(): void
    {
        $constraint = new Xdigit();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result, 'Empty string should fail xdigit validation');
    }

    /**
     * Test Xdigit throws exception when fails.
     */
    public function testXdigitThrowsExceptionWhenFails(): void
    {
        $constraint = new Xdigit();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'abc123g', [$constraint]);
    }
}

