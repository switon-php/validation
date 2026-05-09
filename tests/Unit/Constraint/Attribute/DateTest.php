<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Date;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Date constraint.
 *
 * Tests date validation and conversion between string and timestamp formats.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class DateTest extends TestCase
{
    /**
     * Test Date passes with valid date string.
     */
    public function testDatePassesWithValidDateString(): void
    {
        $constraint = new Date();
        $source = new class {
            public string $date;
        };
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'date';
        $validation->value = '2023-12-25';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Date passes with valid timestamp.
     */
    public function testDatePassesWithValidTimestamp(): void
    {
        $constraint = new Date();
        $source = new class {
            public int $date;
        };
        $timestamp = strtotime('2023-12-25');
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'date';
        $validation->value = $timestamp;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Date converts string to timestamp when field type is int.
     */
    public function testDateConvertsStringToTimestampWhenFieldTypeIsInt(): void
    {
        $constraint = new Date();
        $source = new class {
            public int $date;
        };
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'date';
        $validation->value = '2023-12-25';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertIsInt($validation->value);
    }

    /**
     * Test Date converts timestamp to date string when field type is string.
     */
    public function testDateConvertsTimestampToDateStringWhenFieldTypeIsString(): void
    {
        $constraint = new Date();
        $source = new class {
            public string $date;
        };
        $timestamp = strtotime('2023-12-25 10:30:00');
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'date';
        $validation->value = $timestamp;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertIsString($validation->value);
        $this->assertStringContainsString('2023-12-25', $validation->value);
    }

    /**
     * Test Date uses validation target type metadata for array-source normalization.
     */
    public function testDateUsesValidationTargetTypeMetadata(): void
    {
        $constraint = new Date();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'date';
        $validation->targetType = 'int';
        $validation->value = '2023-12-25';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertIsInt($validation->value);
    }

    /**
     * Test Date applies custom string format when configured.
     */
    public function testDateAppliesCustomStringFormatWhenConfigured(): void
    {
        $constraint = new Date('Y-m-d');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'date';
        $validation->targetType = 'string';
        $validation->value = strtotime('2023-12-25 10:30:00');

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('2023-12-25', $validation->value);
    }

    /**
     * Test Date fails with invalid date string.
     */
    public function testDateFailsWithInvalidDateString(): void
    {
        $constraint = new Date();
        $source = new class {
            public string $date;
        };
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'date';
        $validation->value = 'invalid-date';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Date throws exception when fails.
     */
    public function testDateThrowsExceptionWhenFails(): void
    {
        $constraint = new Date();
        $source = new class {
            public string $date;
        };

        $this->expectException(ValidateFailedException::class);
        // Use manual validation flow - use $validation->validate() which handles error adding
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'date';
        $validation->value = 'invalid-date';
        $validation->validate($constraint); // This will add error if validation fails
        $this->validator->endValidate($validation);
    }
}

