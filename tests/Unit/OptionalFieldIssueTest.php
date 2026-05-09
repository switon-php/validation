<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit;

use Switon\Validating\Attribute\Defaults;
use Switon\Validating\Attribute\Type;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Reproduction test for "Optional and Typed" field issue.
 *
 * Verifies that it is currently impossible to define a field that is:
 * "If present, must be an integer. If missing, ignore."
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class OptionalFieldIssueTest extends TestCase
{
    /**
     * Scenario 1: Field is missing from input, but has a Type constraint.
     *
     * Expectation: Ideally, this should pass (optional).
     * Actual: Fails because missing field becomes null, and null is not an int.
     */
    public function testMissingTypedFieldFailsValidation(): void
    {
        // Arrange
        $source = []; // Empty input
        $constraints = [
            'age' => [new Type('int')] // "age" should be an int
        ];

        // Assert - We expect this to FAIL currently
        $this->expectException(ValidateFailedException::class);
        $this->expectExceptionMessage('The age data type is not int.');

        // Act
        $this->validator->validateValues($source, $constraints);
    }

    /**
     * Scenario 2: Trying to fix it with Defaults(null).
     *
     * Rationale: "Maybe if I set a default value of null, it will work?"
     * Actual: Fails because Type('int') executes AFTER Defaults, and null is still not an int.
     */
    public function testMissingFieldWithDefaultNullAlsoFails(): void
    {
        // Arrange
        $source = [];
        $constraints = [
            // Set default to null if missing, then check if it's an int
            'age' => [new Defaults(null), new Type('int')]
        ];

        // Assert
        $this->expectException(ValidateFailedException::class);

        // Act
        $this->validator->validateValues($source, $constraints);
    }

    /**
     * Scenario 3: Valid input should still work.
     *
     * Just to confirm the validator isn't completely broken.
     */
    public function testPresentTypedFieldPasses(): void
    {
        // Arrange
        $source = ['age' => 18];
        $constraints = [
            'age' => [new Type('int')]
        ];

        // Act
        $result = $this->validator->validateValues($source, $constraints);

        // Assert
        $this->assertEquals(['age' => 18], $result);
    }
}
