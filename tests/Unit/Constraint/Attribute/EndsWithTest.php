<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\EndsWith;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for EndsWith constraint.
 *
 * Tests that string ends with specified needle(s).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class EndsWithTest extends TestCase
{
    /**
     * Test EndsWith passes when string ends with single needle.
     */
    public function testEndsWithPassesWhenStringEndsWithSingleNeedle(): void
    {
        $constraint = new EndsWith('.com');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'example.com';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test EndsWith fails when string does not end with needle.
     */
    public function testEndsWithFailsWhenStringDoesNotEndWithNeedle(): void
    {
        $constraint = new EndsWith('.org');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'example.com';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test EndsWith passes when string ends with any needle in array.
     */
    public function testEndsWithPassesWhenStringEndsWithAnyNeedleInArray(): void
    {
        $constraint = new EndsWith(['.com', '.org', '.net']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'example.org';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test EndsWith fails when string does not end with any needle in array.
     */
    public function testEndsWithFailsWhenStringDoesNotEndWithAnyNeedleInArray(): void
    {
        $constraint = new EndsWith(['.com', '.org', '.net']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'example.io';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test EndsWith throws exception when fails.
     */
    public function testEndsWithThrowsExceptionWhenFails(): void
    {
        $constraint = new EndsWith('.org');

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'example.com', [$constraint]);
    }
}

