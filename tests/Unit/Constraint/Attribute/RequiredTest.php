<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Required;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class RequiredTest extends TestCase
{
    public function testRequiredPassesWhenValueIsPresent(): void
    {
        $constraint = new Required();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'testValue';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testRequiredPassesWhenValueIsZero(): void
    {
        $constraint = new Required();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 0;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testRequiredPassesWhenValueIsEmptyString(): void
    {
        $constraint = new Required();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testRequiredFailsWhenValueIsNull(): void
    {
        $constraint = new Required();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = null;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testRequiredFailsWhenValueIsUnset(): void
    {
        $constraint = new Required();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        unset($validation->value);

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testRequiredThrowsExceptionWhenFails(): void
    {
        $constraint = new Required();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', null, [$constraint]);
    }
}
