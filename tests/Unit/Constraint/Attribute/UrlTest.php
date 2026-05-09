<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Url;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Url constraint.
 *
 * Tests URL validation.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class UrlTest extends TestCase
{
    /**
     * Test Url passes with valid HTTP URL.
     */
    public function testUrlPassesWithValidHttpUrl(): void
    {
        $constraint = new Url();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'http://example.com';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Url passes with valid HTTPS URL.
     */
    public function testUrlPassesWithValidHttpsUrl(): void
    {
        $constraint = new Url();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'https://example.com/path?query=value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Url fails with invalid URL.
     */
    public function testUrlFailsWithInvalidUrl(): void
    {
        $constraint = new Url();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'not a url';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Url fails with empty string.
     */
    public function testUrlFailsWithEmptyString(): void
    {
        $constraint = new Url();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Url throws exception when fails.
     */
    public function testUrlThrowsExceptionWhenFails(): void
    {
        $constraint = new Url();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'invalid-url', [$constraint]);
    }
}

