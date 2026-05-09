<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Defaults;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Defaults constraint.
 *
 * Tests that default value is set when value is null or empty string.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class DefaultsTest extends TestCase
{
    /**
     * Test Defaults sets default when value is null.
     */
    public function testDefaultsSetsDefaultWhenValueIsNull(): void
    {
        $constraint = new Defaults('defaultValue');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = null;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('defaultValue', $validation->value);
    }

    /**
     * Test Defaults sets default when value is empty string.
     */
    public function testDefaultsSetsDefaultWhenValueIsEmptyString(): void
    {
        $constraint = new Defaults('defaultValue');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('defaultValue', $validation->value);
    }

    /**
     * Test Defaults does not set default when value is present.
     */
    public function testDefaultsDoesNotSetDefaultWhenValueIsPresent(): void
    {
        $constraint = new Defaults('defaultValue');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'actualValue';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('actualValue', $validation->value);
    }

    /**
     * Test Defaults works with integer default.
     */
    public function testDefaultsWorksWithIntegerDefault(): void
    {
        $constraint = new Defaults(0);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = null;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(0, $validation->value);
    }

    /**
     * Test Defaults works with array default.
     */
    public function testDefaultsWorksWithArrayDefault(): void
    {
        $defaultArray = ['key' => 'value'];
        $constraint = new Defaults($defaultArray);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = null;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame($defaultArray, $validation->value);
    }

    /**
     * Test Defaults does not set default for zero value.
     */
    public function testDefaultsDoesNotSetDefaultForZeroValue(): void
    {
        $constraint = new Defaults(100);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 0;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(0, $validation->value);
    }
}

