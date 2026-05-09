<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\EqualTo;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for EqualTo constraint.
 *
 * Tests that value equals another field's value.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class EqualToTest extends TestCase
{
    /**
     * Test EqualTo passes when value equals other field value (array source).
     */
    public function testEqualToPassesWhenValueEqualsOtherFieldArray(): void
    {
        $constraint = new EqualTo('password');
        $source = ['password' => 'secret123', 'password_confirm' => 'secret123'];
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'password_confirm';
        $validation->value = 'secret123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test EqualTo fails when value does not equal other field value (array source).
     */
    public function testEqualToFailsWhenValueDoesNotEqualOtherFieldArray(): void
    {
        $constraint = new EqualTo('password');
        $source = ['password' => 'secret123', 'password_confirm' => 'different'];
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'password_confirm';
        $validation->value = 'different';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test EqualTo passes when value equals other field value (object source).
     */
    public function testEqualToPassesWhenValueEqualsOtherFieldObject(): void
    {
        $constraint = new EqualTo('password');
        $source = new class {
            public string $password = 'secret123';
            public string $password_confirm = 'secret123';
        };
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'password_confirm';
        $validation->value = 'secret123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test EqualTo fails when other field does not exist.
     */
    public function testEqualToFailsWhenOtherFieldDoesNotExist(): void
    {
        $constraint = new EqualTo('nonexistent');
        $source = ['field1' => 'value1'];
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'field1';
        $validation->value = 'value1';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test EqualTo uses strict comparison.
     */
    public function testEqualToUsesStrictComparison(): void
    {
        // Arrange - int 1 vs string '1'
        $constraint = new EqualTo('count');
        $source = ['count' => 1, 'confirm' => '1'];
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'confirm';
        $validation->value = '1'; // string '1'

        // Act
        $result = $constraint->validate($validation);

        // Assert - strict comparison: int 1 !== string '1'
        $this->assertFalse($result, 'EqualTo should use strict comparison (=== not ==)');
    }

    /**
     * Test EqualTo throws exception when fails.
     */
    public function testEqualToThrowsExceptionWhenFails(): void
    {
        $constraint = new EqualTo('password');

        $this->expectException(ValidateFailedException::class);
        // Use validateValues which automatically calls endValidate
        $this->validator->validateValues(
            ['password' => 'secret123', 'password_confirm' => 'wrong'],
            ['password_confirm' => [$constraint]]
        );
    }
}

