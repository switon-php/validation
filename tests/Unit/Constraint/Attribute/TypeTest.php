<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Type;
use Switon\Validating\Exception\UnsupportedValidationTypeException;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Type constraint.
 *
 * Tests type validation and conversion for various types: string, int, float, bool, array, object, mixed, iterable, bit.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class TypeTest extends TestCase
{
    /**
     * Test Type with string type passes with string value.
     */
    public function testTypeStringPassesWithStringValue(): void
    {
        $constraint = new Type('string');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'testValue';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('testValue', $validation->value);
    }

    /**
     * Test Type with string type fails with non-string value.
     */
    public function testTypeStringFailsWithNonStringValue(): void
    {
        $constraint = new Type('string');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 123;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with int type converts string to int.
     */
    public function testTypeIntConvertsStringToInt(): void
    {
        $constraint = new Type('int');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '123';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(123, $validation->value);
        $this->assertIsInt($validation->value);
    }

    /**
     * Test Type with int type passes with int value.
     */
    public function testTypeIntPassesWithIntValue(): void
    {
        $constraint = new Type('int');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 123;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(123, $validation->value);
    }

    /**
     * Test Type with int type converts bool to int.
     */
    public function testTypeIntConvertsBoolToInt(): void
    {
        $constraint = new Type('int');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = true;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(1, $validation->value);
    }

    /**
     * Test Type with int type fails with invalid string.
     */
    public function testTypeIntFailsWithInvalidString(): void
    {
        $constraint = new Type('int');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '12.3';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with float type converts string to float.
     */
    public function testTypeFloatConvertsStringToFloat(): void
    {
        $constraint = new Type('float');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '12.5';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(12.5, $validation->value);
        $this->assertIsFloat($validation->value);
    }

    /**
     * Test Type with float type passes with int value.
     *
     * Note: Type constraint accepts int as float (PHP's type system allows this),
     * so we just verify it passes without conversion.
     */
    public function testTypeFloatPassesWithIntValue(): void
    {
        $constraint = new Type('float');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 12;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        // Type constraint accepts int as float without conversion
        $this->assertIsInt($validation->value);
    }

    /**
     * Test Type with float type fails with invalid string.
     */
    public function testTypeFloatFailsWithInvalidString(): void
    {
        $constraint = new Type('float');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'invalid';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with bool type converts string to bool.
     */
    public function testTypeBoolConvertsStringToBool(): void
    {
        $constraint = new Type('bool');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'true';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertTrue($validation->value);
        $this->assertIsBool($validation->value);
    }

    /**
     * Test Type with bool type converts 'false' string to false.
     */
    public function testTypeBoolConvertsFalseStringToFalse(): void
    {
        $constraint = new Type('bool');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'false';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertFalse($validation->value);
    }

    /**
     * Test Type with bool type passes with bool value.
     */
    public function testTypeBoolPassesWithBoolValue(): void
    {
        $constraint = new Type('bool');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = true;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertTrue($validation->value);
    }

    /**
     * Test Type with bool type fails with invalid string.
     */
    public function testTypeBoolFailsWithInvalidString(): void
    {
        $constraint = new Type('bool');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'maybe';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with bit type converts true to 1.
     */
    public function testTypeBitConvertsTrueTo1(): void
    {
        $constraint = new Type('bit');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = true;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(1, $validation->value);
    }

    /**
     * Test Type with bit type converts false to 0.
     */
    public function testTypeBitConvertsFalseTo0(): void
    {
        $constraint = new Type('bit');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = false;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(0, $validation->value);
    }

    /**
     * Test Type with bit type converts 'true' string to 1.
     */
    public function testTypeBitConvertsTrueStringTo1(): void
    {
        $constraint = new Type('bit');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'true';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(1, $validation->value);
    }

    /**
     * Test Type with array type passes with array value.
     */
    public function testTypeArrayPassesWithArrayValue(): void
    {
        $constraint = new Type('array');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = [1, 2, 3];

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertIsArray($validation->value);
    }

    /**
     * Test Type with array type fails with non-array value.
     */
    public function testTypeArrayFailsWithNonArrayValue(): void
    {
        $constraint = new Type('array');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'not array';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with object type passes with object value.
     */
    public function testTypeObjectPassesWithObjectValue(): void
    {
        $constraint = new Type('object');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = new \stdClass();

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertIsObject($validation->value);
    }

    /**
     * Test Type with object type fails with non-object value.
     */
    public function testTypeObjectFailsWithNonObjectValue(): void
    {
        $constraint = new Type('object');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'not object';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with mixed type always passes.
     */
    public function testTypeMixedAlwaysPasses(): void
    {
        $constraint = new Type('mixed');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'any value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Type with iterable type passes with array.
     */
    public function testTypeIterablePassesWithArray(): void
    {
        $constraint = new Type('iterable');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = [1, 2, 3];

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Type with iterable type fails with non-iterable value.
     *
     * Note: Strings are iterable in PHP, so we test with an integer instead.
     */
    public function testTypeIterableFailsWithNonIterableValue(): void
    {
        $constraint = new Type('iterable');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 123; // Integer is not iterable

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with unsupported type throws exception.
     */
    public function testTypeUnsupportedTypeThrowsException(): void
    {
        $constraint = new Type('unsupported');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'test';

        $this->expectException(UnsupportedValidationTypeException::class);
        $constraint->validate($validation);
    }

    /**
     * Test Type throws exception when fails.
     */
    public function testTypeThrowsExceptionWhenFails(): void
    {
        $constraint = new Type('int');

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'invalid', [$constraint]);
    }

    /**
     * Test Type with custom true/false values for bool.
     */
    public function testTypeBoolWithCustomTrueFalseValues(): void
    {
        $constraint = new Type('bool', ['yes'], ['no']);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'yes';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertTrue($validation->value);
    }

    /**
     * Test Type with backed enum converts raw value to case.
     */
    public function testTypeBackedEnumConvertsValue(): void
    {
        $constraint = new Type(TypeTestFixtureBacked::class);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 1;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(TypeTestFixtureBacked::One, $validation->value);
    }

    /**
     * Test Type with backed enum passes when value is already a case.
     */
    public function testTypeBackedEnumPassesWithCaseInstance(): void
    {
        $constraint = new Type(TypeTestFixtureBacked::class);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = TypeTestFixtureBacked::Zero;

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(TypeTestFixtureBacked::Zero, $validation->value);
    }

    /**
     * Test Type with backed enum fails for unknown value.
     */
    public function testTypeBackedEnumFailsForUnknownValue(): void
    {
        $constraint = new Type(TypeTestFixtureBacked::class);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 99;

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Type with unit enum converts case name to case.
     */
    public function testTypeUnitEnumConvertsName(): void
    {
        $constraint = new Type(TypeTestFixtureUnit::class);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'Active';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame(TypeTestFixtureUnit::Active, $validation->value);
    }

    /**
     * Test Type with unit enum fails for unknown name.
     */
    public function testTypeUnitEnumFailsForUnknownName(): void
    {
        $constraint = new Type(TypeTestFixtureUnit::class);
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'Missing';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }
}

enum TypeTestFixtureBacked: int
{
    case Zero = 0;
    case One = 1;
}

enum TypeTestFixtureUnit
{
    case Active;
    case Pending;
}

