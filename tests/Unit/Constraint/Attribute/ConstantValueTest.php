<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\ConstantValue;
use Switon\Validating\Exception\InvalidConstraintSourceException;
use Switon\Validating\Exception\InvalidConstraintTargetException;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class ConstantValueTest extends TestCase
{
    public function testConstantValuePassesWhenValueMatchesClassConstant(): void
    {
        $source = new class {
            public const STATUS_ACTIVE = 1;
            public const STATUS_INACTIVE = 0;
        };

        $constraint = new ConstantValue();
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'status';
        $validation->value = 1;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testConstantValueFailsWhenValueDoesNotMatchAnyConstantValue(): void
    {
        $source = new class {
            public const STATUS_ACTIVE = 1;
            public const STATUS_INACTIVE = 0;
        };

        $constraint = new ConstantValue();
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'status';
        $validation->value = 2;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    public function testConstantValueWorksWithNamedConstantPrefix(): void
    {
        $source = new class {
            public const TYPE_USER = 'user';
            public const TYPE_ADMIN = 'admin';
            public const STATUS_ACTIVE = 1;
        };

        $constraint = new ConstantValue(prefix: 'type');
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'user_type';
        $validation->value = 'user';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testConstantValueCanResolveConstantsFromExplicitClass(): void
    {
        $source = new class {
            public int $status = 1;
        };

        $constraint = new ConstantValue(class: ExternalStatusConstants::class);
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'status';
        $validation->value = 1;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testConstantValueSupportsClassWithExplicitPrefix(): void
    {
        $constraint = new ConstantValue(prefix: 'type', class: ExternalStatusConstants::class);
        $validation = $this->validator->beginValidate(['user_type' => 'admin']);
        $validation->field = 'user_type';
        $validation->value = 'admin';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    public function testConstantValueThrowsServerErrorWhenArraySourceHasNoClass(): void
    {
        $constraint = new ConstantValue();
        $validation = $this->validator->beginValidate(['status' => 1]);
        $validation->field = 'status';
        $validation->value = 1;

        $this->expectException(InvalidConstraintSourceException::class);
        $constraint->validate($validation);
    }

    public function testConstantValueThrowsServerErrorWhenConstantClassIsInvalid(): void
    {
        $constraint = new ConstantValue(class: 'App\\Missing\\StatusConstants');
        $validation = $this->validator->beginValidate(['status' => 1]);
        $validation->field = 'status';
        $validation->value = 1;

        $this->expectException(InvalidConstraintTargetException::class);
        $constraint->validate($validation);
    }

    public function testConstantValueThrowsServerErrorWhenConstantPrefixDoesNotExist(): void
    {
        $source = new class {
            public const STATUS_ACTIVE = 1;
        };

        $constraint = new ConstantValue(prefix: 'type');
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'status';
        $validation->value = 'admin';

        $this->expectException(InvalidConstraintTargetException::class);
        $constraint->validate($validation);
    }

    public function testConstantValueThrowsExceptionWhenValidationFails(): void
    {
        $source = new class {
            public const STATUS_ACTIVE = 1;
        };

        $constraint = new ConstantValue();

        $this->expectException(ValidateFailedException::class);
        $validation = $this->validator->beginValidate($source);
        $validation->field = 'status';
        $validation->value = 999;
        $validation->validate($constraint);
        $this->validator->endValidate($validation);
    }
}

final class ExternalStatusConstants
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_LOCKED = 2;
    public const TYPE_USER = 'user';
    public const TYPE_ADMIN = 'admin';
}
