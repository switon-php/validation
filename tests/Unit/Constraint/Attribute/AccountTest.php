<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Account;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Account constraint.
 *
 * Tests account name validation (starts with lowercase letter, followed by lowercase letters, digits, or underscores, minimum 3 characters).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class AccountTest extends TestCase
{
    /**
     * Test Account passes with valid account name.
     */
    public function testAccountPassesWithValidAccountName(): void
    {
        $constraint = new Account();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'user123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Account passes with account name containing underscore.
     */
    public function testAccountPassesWithAccountNameContainingUnderscore(): void
    {
        $constraint = new Account();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'user_name';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Account fails when starts with uppercase letter.
     */
    public function testAccountFailsWhenStartsWithUppercaseLetter(): void
    {
        $constraint = new Account();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'User123';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Account fails when starts with digit.
     */
    public function testAccountFailsWhenStartsWithDigit(): void
    {
        $constraint = new Account();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123user';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Account fails when too short.
     */
    public function testAccountFailsWhenTooShort(): void
    {
        $constraint = new Account();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'ab'; // Minimum 3 characters

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Account fails with special characters.
     */
    public function testAccountFailsWithSpecialCharacters(): void
    {
        $constraint = new Account();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'user-name';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Account throws exception when fails.
     */
    public function testAccountThrowsExceptionWhenFails(): void
    {
        $constraint = new Account();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'User123', [$constraint]);
    }
}

