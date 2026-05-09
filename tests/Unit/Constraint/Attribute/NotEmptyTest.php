<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\NotEmpty;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for NotEmpty constraint.
 *
 * Tests that value is not empty (not null and not empty string).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class NotEmptyTest extends TestCase
{
    /**
     * Test NotEmpty passes when value is present.
     */
    public function testNotEmptyPassesWhenValueIsPresent(): void
    {
        $constraint = new NotEmpty();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'testValue';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test NotEmpty passes when value is zero.
     */
    public function testNotEmptyPassesWhenValueIsZero(): void
    {
        $constraint = new NotEmpty();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 0;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test NotEmpty fails when value is null.
     */
    public function testNotEmptyFailsWhenValueIsNull(): void
    {
        $constraint = new NotEmpty();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = null;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test NotEmpty fails when value is empty string.
     */
    public function testNotEmptyFailsWhenValueIsEmptyString(): void
    {
        $constraint = new NotEmpty();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test NotEmpty passes when value is false.
     */
    public function testNotEmptyPassesWhenValueIsFalse(): void
    {
        $constraint = new NotEmpty();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = false;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test NotEmpty throws exception when fails.
     */
    public function testNotEmptyThrowsExceptionWhenFails(): void
    {
        $constraint = new NotEmpty();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', '', [$constraint]);
    }
}

