<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Trim;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Trim constraint.
 *
 * Tests that value is trimmed (whitespace removed from start and end).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class TrimTest extends TestCase
{
    /**
     * Test Trim removes whitespace from start and end.
     */
    public function testTrimRemovesWhitespaceFromStartAndEnd(): void
    {
        $constraint = new Trim();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '  test value  ';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Trim removes newlines and tabs.
     */
    public function testTrimRemovesNewlinesAndTabs(): void
    {
        $constraint = new Trim();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = "\n\t  test value  \n\t";

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Trim does not modify already trimmed value.
     */
    public function testTrimDoesNotModifyAlreadyTrimmedValue(): void
    {
        $constraint = new Trim();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'test value';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Trim with custom characters.
     */
    public function testTrimWithCustomCharacters(): void
    {
        $constraint = new Trim('-');
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '---test value---';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('test value', $validation->value);
    }

    /**
     * Test Trim with empty string.
     */
    public function testTrimWithEmptyString(): void
    {
        $constraint = new Trim();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '   ';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
        $this->assertSame('', $validation->value);
    }
}

