<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit;

use Switon\Validating\Tests\Fixtures\AlwaysFailConstraint;
use Switon\Validating\Tests\Fixtures\AlwaysPassConstraint;
use Switon\Validating\Tests\Fixtures\CustomErrorConstraint;
use Switon\Validating\Tests\Fixtures\UppercaseConstraint;
use Switon\Validating\Tests\TestCase;

/**
 * Tests for the Validation context class.
 *
 * Tests the per-call state container that holds field, value, source,
 * and errors during a validation session.
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class ValidationTest extends TestCase
{
    // ========================================================================
    // validate() - fail-fast behavior
    // ========================================================================

    public function testValidateSkipsWhenFieldAlreadyHasError(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'name';
        $validation->value = 'test';
        $validation->addError('First error');

        // Act - second constraint should be skipped entirely
        $result = $validation->validate(new AlwaysPassConstraint());

        // Assert
        $this->assertFalse($result, 'validate() should return false when field already has error');
        $this->assertSame('First error', $validation->getErrors()['name'], 'Original error should be preserved');
    }

    public function testValidateSkipsConstraintExecutionWhenFieldAlreadyHasError(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = 'bad';
        $validation->addError('Existing error');
        $constraint = $this->createMock(\Switon\Validating\ConstraintInterface::class);
        $constraint->expects($this->never())->method('validate');

        // Act
        $result = $validation->validate($constraint);

        // Assert
        $this->assertFalse($result);
        $this->assertSame('Existing error', $validation->getErrors()['email']);
    }

    public function testValidateReturnsTrueWhenConstraintPasses(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'name';
        $validation->value = 'valid';

        // Act
        $result = $validation->validate(new AlwaysPassConstraint());

        // Assert
        $this->assertTrue($result, 'validate() should return true when constraint passes');
        $this->assertEmpty($validation->getErrors(), 'No errors should be added');
    }

    public function testValidateReturnsFalseAndAddsErrorWhenConstraintFails(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'name';
        $validation->value = 'invalid';

        // Act
        $result = $validation->validate(new AlwaysFailConstraint());

        // Assert
        $this->assertFalse($result, 'validate() should return false when constraint fails');
        $this->assertTrue($validation->hasError('name'), 'Error should be added for failed field');
    }

    public function testValidatePreservesConstraintCustomError(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = 'bad';

        // Act - CustomErrorConstraint calls addError() itself
        $result = $validation->validate(new CustomErrorConstraint());

        // Assert
        $this->assertFalse($result, 'validate() should return false');
        $this->assertSame(
            'Custom error for email',
            $validation->getErrors()['email'],
            'Constraint custom error should not be overwritten by default error'
        );
    }

    // ========================================================================
    // validate() - value mutation
    // ========================================================================

    public function testValidateAllowsConstraintToModifyValue(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'name';
        $validation->value = 'hello';

        // Act - UppercaseConstraint converts value to uppercase
        $validation->validate(new UppercaseConstraint());

        // Assert
        $this->assertSame('HELLO', $validation->value, 'Constraint should be able to modify value');
    }

    // ========================================================================
    // addError()
    // ========================================================================

    public function testAddErrorAutoIncludesFieldPlaceholder(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'username';
        $validation->value = '';

        // Act
        $validation->addError('{field} is required');

        // Assert
        $this->assertSame(
            'username is required',
            $validation->getErrors()['username'],
            'Field placeholder should be automatically replaced'
        );
    }

    public function testAddErrorFormatsMultiplePlaceholders(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'age';
        $validation->value = 5;

        // Act
        $validation->addError('{field} must be between {min} and {max}', ['min' => 18, 'max' => 65]);

        // Assert
        $this->assertSame(
            'age must be between 18 and 65',
            $validation->getErrors()['age'],
            'All placeholders should be replaced'
        );
    }

    public function testAddErrorOverwritesPreviousErrorForSameField(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'name';
        $validation->value = '';

        // Act
        $validation->addError('First error');
        $validation->addError('Second error');

        // Assert
        $this->assertSame(
            'Second error',
            $validation->getErrors()['name'],
            'Later addError() should overwrite previous error for same field'
        );
    }

    // ========================================================================
    // hasError() / getErrors()
    // ========================================================================

    public function testHasErrorReturnsFalseForCleanField(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);

        // Assert
        $this->assertFalse($validation->hasError('name'), 'hasError() should return false for field without error');
    }

    public function testHasErrorReturnsTrueAfterAddError(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'name';
        $validation->value = '';
        $validation->addError('required');

        // Assert
        $this->assertTrue($validation->hasError('name'), 'hasError() should return true after addError');
        $this->assertFalse($validation->hasError('email'), 'hasError() should return false for other fields');
    }

    public function testGetErrorsReturnsEmptyArrayInitially(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);

        // Assert
        $this->assertSame([], $validation->getErrors(), 'getErrors() should return empty array initially');
    }

    // ========================================================================
    // Multi-field scenarios
    // ========================================================================

    public function testMultipleFieldsCollectSeparateErrors(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);

        // Act - add errors for different fields
        $validation->field = 'name';
        $validation->value = '';
        $validation->addError('{field} is required');

        $validation->field = 'email';
        $validation->value = 'bad';
        $validation->addError('{field} is invalid');

        // Assert
        $errors = $validation->getErrors();
        $this->assertCount(2, $errors, 'Should have errors for both fields');
        $this->assertSame('name is required', $errors['name']);
        $this->assertSame('email is invalid', $errors['email']);
    }

    public function testFailFastOnlyAffectsCurrentField(): void
    {
        // Arrange
        $validation = $this->validator->beginValidate([]);

        // Act - first field fails
        $validation->field = 'name';
        $validation->value = '';
        $validation->validate(new AlwaysFailConstraint());

        // Act - switch to different field, should still validate
        $validation->field = 'email';
        $validation->value = 'test@example.com';
        $result = $validation->validate(new AlwaysPassConstraint());

        // Assert
        $this->assertTrue($result, 'Fail-fast should only affect the field that has the error');
        $this->assertTrue($validation->hasError('name'));
        $this->assertFalse($validation->hasError('email'));
    }

    // ========================================================================
    // Source access
    // ========================================================================

    public function testSourceIsAccessibleAsArray(): void
    {
        // Arrange
        $source = ['name' => 'mark', 'age' => 30];

        // Act
        $validation = $this->validator->beginValidate($source);

        // Assert
        $this->assertSame($source, $validation->source, 'Source array should be accessible');
    }

    public function testSourceIsAccessibleAsObject(): void
    {
        // Arrange
        $source = new \stdClass();
        $source->name = 'mark';

        // Act
        $validation = $this->validator->beginValidate($source);

        // Assert
        $this->assertSame($source, $validation->source, 'Source object should be accessible');
    }
}
