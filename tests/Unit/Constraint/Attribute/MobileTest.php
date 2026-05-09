<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Mobile;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Mobile constraint.
 *
 * Tests Chinese mobile phone number validation (1[3-9]XXXXXXXXX format).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class MobileTest extends TestCase
{
    /**
     * Test Mobile passes with valid mobile number starting with 13.
     */
    public function testMobilePassesWithValidMobileNumberStartingWith13(): void
    {
        $constraint = new Mobile();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '13800138000';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Mobile passes with valid mobile number starting with 15.
     */
    public function testMobilePassesWithValidMobileNumberStartingWith15(): void
    {
        $constraint = new Mobile();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '15800158000';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Mobile passes with valid mobile number starting with 19.
     */
    public function testMobilePassesWithValidMobileNumberStartingWith19(): void
    {
        $constraint = new Mobile();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '19800198000';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Mobile fails with invalid first digit.
     */
    public function testMobileFailsWithInvalidFirstDigit(): void
    {
        $constraint = new Mobile();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '12800128000'; // Starts with 12, not 1[3-9]

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Mobile fails with wrong length.
     */
    public function testMobileFailsWithWrongLength(): void
    {
        $constraint = new Mobile();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '1380013800'; // 10 digits, should be 11

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Mobile throws exception when fails.
     */
    public function testMobileThrowsExceptionWhenFails(): void
    {
        $constraint = new Mobile();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'invalid-mobile', [$constraint]);
    }
}

