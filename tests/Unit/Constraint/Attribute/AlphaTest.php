<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Alpha;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Alpha constraint.
 *
 * Tests alphabetic string validation (letters only).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class AlphaTest extends TestCase
{
    /**
     * Test Alpha passes with alphabetic string.
     */
    public function testAlphaPassesWithAlphabeticString(): void
    {
        $constraint = new Alpha();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Alpha fails with digits.
     */
    public function testAlphaFailsWithDigits(): void
    {
        $constraint = new Alpha();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc123';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Alpha fails with special characters.
     */
    public function testAlphaFailsWithSpecialCharacters(): void
    {
        $constraint = new Alpha();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc-def';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Alpha fails with empty string.
     */
    public function testAlphaFailsWithEmptyString(): void
    {
        $constraint = new Alpha();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result, 'Empty string should fail alpha validation');
    }

    /**
     * Test Alpha throws exception when fails.
     */
    public function testAlphaThrowsExceptionWhenFails(): void
    {
        $constraint = new Alpha();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'abc123', [$constraint]);
    }
}

