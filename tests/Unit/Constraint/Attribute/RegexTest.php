<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Regex;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Regex constraint.
 *
 * Tests regex pattern validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class RegexTest extends TestCase
{
    /**
     * Test Regex passes when pattern matches.
     */
    public function testRegexPassesWhenPatternMatches(): void
    {
        $constraint = new Regex('/^[a-z]+$/');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Regex fails when pattern does not match.
     */
    public function testRegexFailsWhenPatternDoesNotMatch(): void
    {
        $constraint = new Regex('/^[a-z]+$/');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'abc123';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Regex works with complex patterns.
     */
    public function testRegexWorksWithComplexPatterns(): void
    {
        $constraint = new Regex('/^\d{4}-\d{2}-\d{2}$/');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '2023-12-25';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Regex fails with empty string when pattern requires content.
     */
    public function testRegexFailsWithEmptyStringWhenPatternRequiresContent(): void
    {
        $constraint = new Regex('/^[a-z]+$/');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result, 'Empty string should fail when pattern requires one or more characters');
    }

    /**
     * Test Regex throws exception when fails.
     */
    public function testRegexThrowsExceptionWhenFails(): void
    {
        $constraint = new Regex('/^[a-z]+$/');

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'abc123', [$constraint]);
    }
}

