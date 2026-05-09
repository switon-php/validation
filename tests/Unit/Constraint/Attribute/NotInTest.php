<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\NotIn;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for NotIn constraint.
 *
 * Tests that value is not in the specified array.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class NotInTest extends TestCase
{
    /**
     * Test NotIn passes when value is not in array.
     */
    public function testNotInPassesWhenValueIsNotInArray(): void
    {
        $constraint = new NotIn(['value1', 'value2', 'value3']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'value4';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test NotIn fails when value is in array.
     */
    public function testNotInFailsWhenValueIsInArray(): void
    {
        $constraint = new NotIn(['value1', 'value2', 'value3']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'value2';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test NotIn uses strict comparison.
     */
    public function testNotInUsesStrictComparison(): void
    {
        $constraint = new NotIn(['1', '2', '3']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 1; // Integer 1 !== string '1'

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test NotIn throws exception when fails.
     */
    public function testNotInThrowsExceptionWhenFails(): void
    {
        $constraint = new NotIn(['forbidden1', 'forbidden2']);

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'forbidden1', [$constraint]);
    }
}

