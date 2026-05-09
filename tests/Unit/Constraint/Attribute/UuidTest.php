<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Uuid;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Uuid constraint.
 *
 * Tests UUID format validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class UuidTest extends TestCase
{
    /**
     * Test Uuid passes with valid UUID.
     */
    public function testUuidPassesWithValidUuid(): void
    {
        $constraint = new Uuid();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '550e8400-e29b-41d4-a716-446655440000';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Uuid passes with uppercase UUID.
     */
    public function testUuidPassesWithUppercaseUuid(): void
    {
        $constraint = new Uuid();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '550E8400-E29B-41D4-A716-446655440000';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Uuid fails with invalid format.
     */
    public function testUuidFailsWithInvalidFormat(): void
    {
        $constraint = new Uuid();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '550e8400-e29b-41d4-a716';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Uuid fails with invalid characters.
     */
    public function testUuidFailsWithInvalidCharacters(): void
    {
        $constraint = new Uuid();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '550e8400-e29b-41d4-a716-44665544000g'; // 'g' is invalid

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Uuid throws exception when fails.
     */
    public function testUuidThrowsExceptionWhenFails(): void
    {
        $constraint = new Uuid();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'invalid-uuid', [$constraint]);
    }
}

