<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Decimal;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Decimal constraint.
 *
 * Tests decimal number validation (M = total digits, D = decimal places).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class DecimalTest extends TestCase
{
    /**
     * Test Decimal passes with valid decimal within limits.
     */
    public function testDecimalPassesWithValidDecimalWithinLimits(): void
    {
        $constraint = new Decimal(10, 2); // M=10, D=2
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123.45';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Decimal fails when decimal places exceed D.
     */
    public function testDecimalFailsWhenDecimalPlacesExceedD(): void
    {
        $constraint = new Decimal(10, 2); // D=2
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123.456'; // 3 decimal places

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Decimal fails when total digits exceed M.
     */
    public function testDecimalFailsWhenTotalDigitsExceedM(): void
    {
        $constraint = new Decimal(5, 2); // M=5, D=2
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '12345.67'; // Too many digits

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Decimal passes with integer (no decimal part).
     */
    public function testDecimalPassesWithInteger(): void
    {
        $constraint = new Decimal(10, 2);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Decimal fails with non-numeric value.
     */
    public function testDecimalFailsWithNonNumericValue(): void
    {
        $constraint = new Decimal(10, 2);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Decimal works with negative numbers.
     */
    public function testDecimalWorksWithNegativeNumbers(): void
    {
        $constraint = new Decimal(10, 2);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '-123.45';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Decimal correctly counts only digits (not decimal point) for M check.
     *
     * DECIMAL(5,2) should accept '123.45' because total digits = 5 (3 integer + 2 decimal).
     * Previously this was rejected because the decimal point was counted in the total.
     */
    public function testDecimalCountsOnlyDigitsNotDecimalPoint(): void
    {
        // Arrange
        $constraint = new Decimal(5, 2); // M=5, D=2
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123.45'; // 3 integer + 2 decimal = 5 total digits

        // Act
        $result = $constraint->validate($validation);

        // Assert
        $this->assertTrue($result, 'DECIMAL(5,2) should accept 123.45 (5 total digits)');
    }

    /**
     * Test Decimal rejects when total digits exceed M.
     */
    public function testDecimalRejectsWhenTotalDigitsExceedM(): void
    {
        // Arrange
        $constraint = new Decimal(5, 2); // M=5, D=2, max integer digits = 3
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '1234.56'; // 4 integer + 2 decimal = 6 total digits > 5

        // Act
        $result = $constraint->validate($validation);

        // Assert
        $this->assertFalse($result, 'DECIMAL(5,2) should reject 1234.56 (6 total digits > 5)');
    }

    /**
     * Test Decimal throws exception when fails.
     */
    public function testDecimalThrowsExceptionWhenFails(): void
    {
        $constraint = new Decimal(5, 2);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', '123456.78', [$constraint]);
    }
}

