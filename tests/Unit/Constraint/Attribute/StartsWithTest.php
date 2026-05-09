<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\StartsWith;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for StartsWith constraint.
 *
 * Tests that string starts with specified needle(s).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class StartsWithTest extends TestCase
{
    /**
     * Test StartsWith passes when string starts with single needle.
     */
    public function testStartsWithPassesWhenStringStartsWithSingleNeedle(): void
    {
        $constraint = new StartsWith('http');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'http://example.com';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test StartsWith fails when string does not start with needle.
     */
    public function testStartsWithFailsWhenStringDoesNotStartWithNeedle(): void
    {
        $constraint = new StartsWith('https');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'http://example.com';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test StartsWith passes when string starts with any needle in array.
     */
    public function testStartsWithPassesWhenStringStartsWithAnyNeedleInArray(): void
    {
        $constraint = new StartsWith(['http://', 'https://']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'https://example.com';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test StartsWith fails when string does not start with any needle in array.
     */
    public function testStartsWithFailsWhenStringDoesNotStartWithAnyNeedleInArray(): void
    {
        $constraint = new StartsWith(['http://', 'https://']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'ftp://example.com';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test StartsWith throws exception when fails.
     */
    public function testStartsWithThrowsExceptionWhenFails(): void
    {
        $constraint = new StartsWith('https');

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'http://example.com', [$constraint]);
    }
}

