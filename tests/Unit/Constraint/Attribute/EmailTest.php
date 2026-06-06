<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Email;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class EmailTest extends TestCase
{
    public function testEmailPassesWithValidEmail(): void
    {
        $constraint = new Email();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = 'test@example.com';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test@example.com', $validation->value);
    }

    public function testEmailConvertsToLowercase(): void
    {
        $constraint = new Email();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = 'TEST@EXAMPLE.COM';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test@example.com', $validation->value);
    }

    public function testEmailFailsWithInvalidEmail(): void
    {
        $constraint = new Email();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = 'invalid-email';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testEmailFailsWithEmptyString(): void
    {
        $constraint = new Email();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testEmailFailsWithNonStringValue(): void
    {
        $constraint = new Email();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = 123;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testEmailThrowsExceptionWhenFails(): void
    {
        $constraint = new Email();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('email', 'invalid-email', [$constraint]);
    }
}
