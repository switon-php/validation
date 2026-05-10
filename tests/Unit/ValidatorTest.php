<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit;

use Switon\Core\FilesystemInterface;
use Switon\Core\LocaleInterface;
use Switon\Core\Filesystem;
use Switon\Core\TranslatorInterface;
use Switon\Validating\Tests\Fixtures\CatalogTranslator;
use Switon\Validating\Attribute\Defaults;
use Switon\Validating\Attribute\Email;
use Switon\Validating\Attribute\EqualTo;
use Switon\Validating\Attribute\Required;
use Switon\Validating\Exception\LocaleTemplateNotFoundException;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\Fixtures\AlwaysFailConstraint;
use Switon\Validating\Tests\Fixtures\AlwaysPassConstraint;
use Switon\Validating\Tests\Fixtures\CustomErrorConstraint;
use Switon\Validating\Tests\Fixtures\UppercaseConstraint;
use Switon\Validating\Tests\TestCase;
use Switon\Validating\Validator;
use Switon\Validating\Validation;
use function file_put_contents;
use function glob;
use function is_dir;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;
use function var_export;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class ValidationValidatorTest extends TestCase
{
    public function testValidateValueWithValidValue(): void
    {
        $constraint = new AlwaysPassConstraint();

        $result = $this->validator->validateValue('testField', 'testValue', [$constraint]);

        $this->assertSame('testValue', $result);
    }

    public function testValidateValueWithInvalidValue(): void
    {
        $constraint = new AlwaysFailConstraint();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'testValue', [$constraint]);
    }

    public function testValidateValuesWithValidValues(): void
    {
        $source = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];
        $constraints = [
            'field1' => [new AlwaysPassConstraint()],
            'field2' => [new AlwaysPassConstraint()],
        ];

        $result = $this->validator->validateValues($source, $constraints);

        $this->assertSame($source, $result);
    }

    public function testValidateValuesWithInvalidValues(): void
    {
        $source = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];
        $constraints = [
            'field1' => [new AlwaysPassConstraint()],
            'field2' => [new AlwaysFailConstraint()],
        ];

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValues($source, $constraints);
    }

    public function testValidateValuesWithMultipleConstraints(): void
    {
        $source = ['field1' => 'value1'];
        $constraints = [
            'field1' => [
                new AlwaysPassConstraint(),
                new AlwaysFailConstraint(),
                new AlwaysPassConstraint(),
            ],
        ];

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValues($source, $constraints);
    }

    public function testValidateValuesWithSingleConstraint(): void
    {
        $source = ['field1' => 'value1'];
        $constraints = [
            'field1' => new AlwaysPassConstraint(),
        ];

        $result = $this->validator->validateValues($source, $constraints);

        $this->assertArrayHasKey('field1', $result);
        $this->assertSame('value1', $result['field1']);
    }

    public function testBeginValidateCreatesValidationObject(): void
    {
        $source = ['field1' => 'value1'];

        $validation = $this->validator->beginValidate($source);

        $this->assertInstanceOf(Validation::class, $validation);
        $this->assertSame($source, $validation->source);
    }

    public function testEndValidateThrowsExceptionWhenHasErrors(): void
    {
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'testValue';
        $validation->addError('test.message', []);

        $this->expectException(ValidateFailedException::class);
        $this->validator->endValidate($validation);
    }

    public function testEndValidateDoesNotThrowWhenNoErrors(): void
    {
        $validation = $this->validator->beginValidate([]);

        $this->validator->endValidate($validation);

        $this->expectNotToPerformAssertions();
    }

    public function testFormatMessageFormatsWithPlaceholders(): void
    {
        $message = 'Switon\Validating\Attribute\Required';
        $placeholders = ['field' => 'username'];

        $result = $this->validator->formatMessage($message, $placeholders);

        $this->assertStringContainsString('username', $result);
        $this->assertStringNotContainsString('{field}', $result);
    }

    public function testFormatMessageUsesDefaultTemplateForUnknownFqcn(): void
    {
        // Arrange - use a FQCN that is not in the templates (falls back to default)
        $message = 'Switon\Validating\Attribute\NonExistent';
        $placeholders = ['field' => 'testField'];

        // Act
        $result = $this->validator->formatMessage($message, $placeholders);

        // Assert - should use default template "The {field} is invalid."
        $this->assertStringContainsString('testField', $result);
        $this->assertStringContainsString('invalid', $result);
    }

    public function testFormatMessageMergesTemplatesFromMultipleDirectories(): void
    {
        $validator = $this->makeValidatorWithLocaleResources(
            templateDataByLocale: [
                'en' => [
                    'default' => 'The {field} is invalid.',
                    Required::class => 'The {field} field is required.',
                    'labels' => [
                        'ValidationValidatorTest::email' => 'Email',
                    ],
                ],
            ],
            extraTemplateDataSets: [[
                'en' => [
                    'Vendor\\App\\DictValue' => 'The {field} must be a valid dict value.',
                    Required::class => 'Please provide {field}.',
                    'labels' => [
                        'ValidationValidatorTest::scene' => 'Scene',
                    ],
                ],
            ]],
        );

        $required = $validator->formatMessage(Required::class, [
            'field' => 'email',
            '_sourceClass' => self::class,
        ]);
        $dict = $validator->formatMessage('Vendor\\App\\DictValue', [
            'field' => 'scene',
            '_sourceClass' => self::class,
        ]);

        $this->assertSame('Please provide Email.', $required);
        $this->assertSame('The Scene must be a valid dict value.', $dict);
    }

    public function testFormatMessageUsesTemplateLabelForShortSourceClassKey(): void
    {
        $validator = $this->makeValidatorWithResources(
            templateData: [
                'default' => 'The {field} is invalid.',
                Required::class => 'The {field} field is required.',
                'labels' => [
                    'ValidationValidatorTest::email' => '邮箱',
                ],
            ],
            validatorConfig: ['translateField' => false],
        );

        $result = $validator->formatMessage(Required::class, [
            'field' => 'email',
            '_sourceClass' => self::class,
        ]);

        $this->assertSame('The 邮箱 field is required.', $result);
    }

    public function testFormatMessageFallsBackToStandardI18nFieldLabelWithShortSourceClassKey(): void
    {
        $validator = $this->makeValidatorWithResources(
            templateData: [
                'default' => 'The {field} is invalid.',
                Required::class => 'The {field} field is required.',
            ],
            translations: [
                'validation.labels.ValidationValidatorTest::email' => '邮箱',
            ],
        );

        $result = $validator->formatMessage(Required::class, [
            'field' => 'email',
            '_sourceClass' => self::class,
        ]);

        $this->assertSame('The 邮箱 field is required.', $result);
    }

    public function testFormatMessageTreatsNonFqcnAsCustomTemplate(): void
    {
        // Arrange - non-FQCN string is treated as custom message template
        $message = '{field} is not valid';
        $placeholders = ['field' => 'testField'];

        // Act
        $result = $this->validator->formatMessage($message, $placeholders);

        // Assert - should use the message directly as template
        $this->assertSame('testField is not valid', $result);
    }

    public function testFormatMessageHandlesNonStringPlaceholders(): void
    {
        $message = 'Switon\Validating\Attribute\Range';
        $placeholders = [
            'field' => 'testField',
            'min' => 10,
            'max' => 20,
        ];

        $result = $this->validator->formatMessage($message, $placeholders);

        $this->assertStringContainsString('testField', $result);
        $this->assertStringContainsString('10', $result);
        $this->assertStringContainsString('20', $result);
        $this->assertStringNotContainsString('{min}', $result);
        $this->assertStringNotContainsString('{max}', $result);
    }

    /**
     * Test that custom message on constraint is used directly instead of falling back to default.
     *
     * Previously, custom messages were silently ignored because getTemplate() treated
     * them as template keys and fell back to the default template.
     */
    public function testCustomMessageOnConstraintIsUsedDirectly(): void
    {
        // Arrange
        $customMessage = '{field} is required!';
        $constraint = new Required(message: $customMessage);

        // Act
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'username';
        $validation->value = null;
        $validation->validate($constraint);
        $errors = $validation->getErrors();

        // Assert - custom message should be used, not the default template
        $this->assertSame('username is required!', $errors['username']);
    }

    /**
     * Test that custom message with placeholders works correctly.
     */
    public function testCustomMessageWithPlaceholders(): void
    {
        // Arrange
        $result = $this->validator->formatMessage(
            'The {field} must be at least {min} chars',
            ['field' => 'password', 'min' => 8]
        );

        // Assert
        $this->assertSame('The password must be at least 8 chars', $result);
    }

    public function testConstraintLabelOverridesFieldPlaceholder(): void
    {
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'username';
        $validation->value = null;
        $validation->validate(new Required(label: '用户名'));

        $this->assertSame('The 用户名 field is required.', $validation->getErrors()['username']);
    }

    public function testInheritedConstraintUsesLabelAsFirstPositionalArgument(): void
    {
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'username';
        $validation->value = null;
        $validation->validate(new Required('用户名'));

        $this->assertSame('The 用户名 field is required.', $validation->getErrors()['username']);
    }

    public function testConstraintLabelsResolveOtherFieldPlaceholder(): void
    {
        try {
            $this->validator->validateValues(
                ['password' => 'secret', 'password_confirm' => 'mismatch'],
                [
                    'password_confirm' => [
                        new EqualTo(
                            'password',
                            label: '确认密码',
                            labels: ['password' => '密码'],
                        ),
                    ],
                ],
            );
            $this->fail('Expected ValidateFailedException');
        } catch (ValidateFailedException $e) {
            $this->assertSame(
                'The 确认密码 must be equal to 密码.',
                $e->getErrors()['password_confirm'],
            );
        }
    }

    public function testEqualToUsesLabelArgumentsBeforeCustomMessage(): void
    {
        try {
            $this->validator->validateValues(
                ['password' => 'secret', 'password_confirm' => 'mismatch'],
                [
                    'password_confirm' => [
                        new EqualTo(
                            'password',
                            '确认密码',
                            ['password' => '密码'],
                            '{field} must match {otherField}.',
                        ),
                    ],
                ],
            );
            $this->fail('Expected ValidateFailedException');
        } catch (ValidateFailedException $e) {
            $this->assertSame(
                '确认密码 must match 密码.',
                $e->getErrors()['password_confirm'],
            );
        }
    }

    public function testFormatMessageResolvesFieldsPlaceholderArrayWithLabels(): void
    {
        $result = $this->validator->formatMessage('{fields}', [
            'fields' => ['email', 'username'],
            '_labels' => ['email' => '邮箱', 'username' => '用户名'],
        ]);

        $this->assertSame('邮箱 + 用户名', $result);
    }

    public function testConstraintLabelCanTranslateWhenEnabled(): void
    {
        $validator = $this->makeValidatorWithResources(
            templateData: [
                'default' => 'The {field} is invalid.',
                Required::class => 'The {field} field is required.',
            ],
            translations: [
                'validation.field.email' => '邮箱',
            ],
            validatorConfig: [
                'translateLabel' => true,
                'translateField' => false,
            ],
        );

        $validation = $validator->beginValidate([]);
        $validation->field = 'email';
        $validation->value = null;
        $validation->validate(new Required(label: 'validation.field.email'));

        $this->assertSame('The 邮箱 field is required.', $validation->getErrors()['email']);
    }

    /**
     * Test that constraint's own addError() is not overwritten by default error.
     *
     * When a constraint calls $validation->addError() itself and returns false,
     * Validation::validate() should preserve the custom error.
     */
    public function testConstraintCustomErrorIsNotOverwritten(): void
    {
        // Arrange
        $constraint = new CustomErrorConstraint();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = 'testValue';

        // Act
        $validation->validate($constraint);
        $errors = $validation->getErrors();

        // Assert - constraint's custom error should be preserved
        $this->assertSame('Custom error for testField', $errors['testField']);
    }

    // ========================================================================
    // validateValues() - edge cases
    // ========================================================================

    public function testValidateValuesReturnsTransformedValues(): void
    {
        // Arrange - Email constraint converts to lowercase
        $source = ['email' => 'USER@EXAMPLE.COM'];
        $constraints = ['email' => [new Email()]];

        // Act
        $result = $this->validator->validateValues($source, $constraints);

        // Assert
        $this->assertSame(
            'user@example.com',
            $result['email'],
            'validateValues() should return transformed values from constraints'
        );
    }

    public function testValidateValuesCollectsAllFieldErrors(): void
    {
        // Arrange - both fields will fail
        $source = ['field1' => 'v1', 'field2' => 'v2'];
        $constraints = [
            'field1' => [new AlwaysFailConstraint()],
            'field2' => [new AlwaysFailConstraint()],
        ];

        // Act
        try {
            $this->validator->validateValues($source, $constraints);
            $this->fail('Expected ValidateFailedException');
        } catch (ValidateFailedException $e) {
            // Assert - both fields should have errors
            $errors = $e->getErrors();
            $this->assertCount(2, $errors, 'Both fields should have errors');
            $this->assertArrayHasKey('field1', $errors);
            $this->assertArrayHasKey('field2', $errors);
        }
    }

    public function testValidateValuesWithEmptyConstraints(): void
    {
        // Arrange
        $source = ['name' => 'mark'];

        // Act
        $result = $this->validator->validateValues($source, []);

        // Assert
        $this->assertSame([], $result, 'Empty constraints should return empty result');
    }

    public function testValidateValuesMissingFieldExcludedFromResult(): void
    {
        // Arrange - field 'age' is not in source
        $source = ['name' => 'mark'];
        $constraints = [
            'name' => [new AlwaysPassConstraint()],
            'age' => [new AlwaysPassConstraint()],
        ];

        // Act
        $result = $this->validator->validateValues($source, $constraints);

        // Assert - missing fields should NOT appear in result (prevents data pollution)
        $this->assertSame('mark', $result['name']);
        $this->assertArrayNotHasKey('age', $result, 'Missing fields should be excluded from result');
    }

    public function testValidateValuesExplicitNullFieldIsPreserved(): void
    {
        // Arrange - field 'age' is explicitly set to null in source
        $source = ['name' => 'mark', 'age' => null];
        $constraints = [
            'name' => [new AlwaysPassConstraint()],
            'age' => [new AlwaysPassConstraint()],
        ];

        // Act
        $result = $this->validator->validateValues($source, $constraints);

        // Assert - explicitly null fields should be preserved
        $this->assertSame('mark', $result['name']);
        $this->assertArrayHasKey('age', $result, 'Explicitly null fields should be included');
        $this->assertNull($result['age']);
    }

    public function testValidateValuesWithDefaultsConstraintSetsDefaultValue(): void
    {
        // Arrange - missing field with Defaults constraint
        $source = ['name' => 'mark'];
        $constraints = [
            'name' => [new AlwaysPassConstraint()],
            'role' => [new Defaults('user'), new AlwaysPassConstraint()],
        ];

        // Act
        $result = $this->validator->validateValues($source, $constraints);

        // Assert
        $this->assertSame('mark', $result['name']);
        $this->assertSame('user', $result['role'], 'Defaults constraint should set default value for missing field');
    }

    public function testValidateValuesStopsOnFirstErrorPerField(): void
    {
        // Arrange - first constraint fails, second (uppercase) should not execute
        $source = ['name' => 'hello'];
        $constraints = [
            'name' => [new AlwaysFailConstraint(), new UppercaseConstraint()],
        ];

        // Act
        try {
            $this->validator->validateValues($source, $constraints);
            $this->fail('Expected ValidateFailedException');
        } catch (ValidateFailedException $e) {
            // Assert - value should NOT be uppercased since first constraint failed
            $this->assertArrayHasKey('name', $e->getErrors());
        }
    }

    // ========================================================================
    // validateValue()
    // ========================================================================

    public function testValidateValueReturnsTransformedValue(): void
    {
        // Arrange
        $constraint = new Email();

        // Act
        $result = $this->validator->validateValue('email', 'USER@EXAMPLE.COM', [$constraint]);

        // Assert
        $this->assertSame('user@example.com', $result, 'validateValue() should return transformed value');
    }

    // ========================================================================
    // beginValidate() / endValidate()
    // ========================================================================

    public function testBeginValidateWithObjectSource(): void
    {
        // Arrange
        $source = new \stdClass();
        $source->name = 'mark';

        // Act
        $validation = $this->validator->beginValidate($source);

        // Assert
        $this->assertInstanceOf(Validation::class, $validation);
        $this->assertSame($source, $validation->source, 'Object source should be accessible');
    }

    // ========================================================================
    // formatMessage() - locale fallback
    // ========================================================================

    public function testFormatMessageUsesLocaleWithRegionFallback(): void
    {
        // Arrange - set locale to 'en-US', should fall back to 'en'
        $this->locale->expects($this->any())->method('get')->willReturn('en-us');

        // Act
        $result = $this->validator->formatMessage(
            'Switon\\Validating\\Attribute\\Required',
            ['field' => 'username']
        );

        // Assert - should use 'en' template since 'en-us' not available
        $this->assertStringContainsString('username', $result);
        $this->assertStringNotContainsString('{field}', $result);
    }

    public function testFormatMessageNormalizesUppercaseRegionLocale(): void
    {
        $locale = $this->createMock(\Switon\Core\LocaleInterface::class);
        $locale->method('get')->willReturn('zh-CN');
        $locale->method('set')->willReturnSelf();
        $this->container->replace(\Switon\Core\LocaleInterface::class, $locale);

        $validator = $this->container->make(\Switon\Validating\Validator::class, [
            'dirs' => [$this->templateDir],
        ]);

        $result = $validator->formatMessage(
            'Switon\\Validating\\Attribute\\Required',
            ['field' => 'username']
        );

        $this->assertSame('username 是必填项', $result);
    }

    public function testFormatMessageNormalizesUnderscoreRegionLocale(): void
    {
        $locale = $this->createMock(\Switon\Core\LocaleInterface::class);
        $locale->method('get')->willReturn('zh_CN');
        $locale->method('set')->willReturnSelf();
        $this->container->replace(\Switon\Core\LocaleInterface::class, $locale);

        $validator = $this->container->make(\Switon\Validating\Validator::class, [
            'dirs' => [$this->templateDir],
        ]);

        $result = $validator->formatMessage(
            'Switon\\Validating\\Attribute\\Required',
            ['field' => 'username']
        );

        $this->assertSame('username 是必填项', $result);
    }

    public function testFormatMessageThrowsForUnavailableLocale(): void
    {
        // Arrange - set locale to one with no template file
        // Need a fresh validator where filesystem returns no files
        $filesystem = $this->createMock(\Switon\Core\Filesystem::class);
        $filesystem->expects($this->any())->method('glob')->willReturn([]);

        $this->container->replace(\Switon\Core\FilesystemInterface::class, $filesystem);

        $validator = $this->container->make(\Switon\Validating\Validator::class, [
            'dirs' => [$this->templateDir],
        ]);

        // Act & Assert
        $this->expectException(LocaleTemplateNotFoundException::class);
        $validator->formatMessage('Switon\\Validating\\Attribute\\Required', ['field' => 'test']);
    }

    // ========================================================================
    // AbstractConstraint - getMessage()
    // ========================================================================

    public function testGetMessageReturnsClassNameWhenNoCustomMessage(): void
    {
        // Arrange
        $constraint = new AlwaysFailConstraint();

        // Act
        $message = $constraint->getMessage();

        // Assert
        $this->assertSame(
            AlwaysFailConstraint::class,
            $message,
            'getMessage() should return FQCN when no custom message provided'
        );
    }

    public function testGetMessageReturnsCustomMessageWhenProvided(): void
    {
        // Arrange
        $constraint = new Required(message: 'Custom required message');

        // Act
        $message = $constraint->getMessage();

        // Assert
        $this->assertSame(
            'Custom required message',
            $message,
            'getMessage() should return custom message when provided'
        );
    }

    // ========================================================================
    // ValidateFailedException
    // ========================================================================

    public function testValidateFailedExceptionContainsAllErrors(): void
    {
        // Arrange
        $source = ['name' => null, 'email' => 'invalid'];
        $constraints = [
            'name' => [new Required()],
            'email' => [new Email()],
        ];

        // Act
        try {
            $this->validator->validateValues($source, $constraints);
            $this->fail('Expected ValidateFailedException');
        } catch (ValidateFailedException $e) {
            // Assert
            $this->assertSame(400, $e->getStatusCode(), 'Status code should be 400');
            $errors = $e->getErrors();
            $this->assertCount(2, $errors, 'Should contain errors for both fields');
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);

            // Verify JSON format
            $json = $e->getJson();
            $this->assertSame(-1, $json['code'], 'JSON code should be -1');
            $this->assertArrayHasKey('validator.errors', $json['data']);
        }
    }

    public function testValidateFailedExceptionLocalizesApiErrorsAcrossLocales(): void
    {
        $locale = new class implements LocaleInterface {
            public function __construct(
                private string $locale = 'en',
                private string $default = 'en',
            )
            {
            }

            public function get(): string
            {
                return $this->locale;
            }

            public function set(string $locale): static
            {
                $this->locale = $locale;
                return $this;
            }

            public function getDefault(): string
            {
                return $this->default;
            }
        };

        $this->container->remove(LocaleInterface::class);
        $this->container->set(LocaleInterface::class, $locale);

        $validator = $this->makeValidatorWithLocaleResources(
            templateDataByLocale: [
                'en' => [
                    'default' => 'The {field} is invalid.',
                    Required::class => 'The {field} field is required.',
                ],
                'zh-cn' => [
                    'default' => '{field} 不合法',
                    Required::class => '{field} 是必填项',
                ],
                'zh' => [
                    'default' => '{field} 不合法',
                    Required::class => '{field} 为必填项',
                ],
            ],
            translationsByLocale: [
                'en' => [
                    'validation.labels.ValidationValidatorTest::email' => 'Email address',
                ],
                'zh-cn' => [
                    'validation.labels.ValidationValidatorTest::email' => '邮箱地址',
                ],
                'zh' => [
                    'validation.labels.ValidationValidatorTest::email' => '電郵地址',
                ],
            ],
        );

        $scenarios = [
            'en' => 'The Email address field is required.',
            'zh-cn' => '邮箱地址 是必填项',
            'zh-hk' => '電郵地址 为必填项',
        ];

        foreach ($scenarios as $requestedLocale => $expectedMessage) {
            $locale->set($requestedLocale);

            $validation = $validator->beginValidate($this);
            $validation->field = 'email';
            $validation->value = null;
            $validation->validate(new Required());

            try {
                $validator->endValidate($validation);
                $this->fail('Expected ValidateFailedException');
            } catch (ValidateFailedException $e) {
                $this->assertSame(['email' => $expectedMessage], $e->getErrors());

                $json = $e->getJson();
                $this->assertSame(-1, $json['code']);
                $this->assertSame(['email' => $expectedMessage], $json['data']['validator.errors']);
                $this->assertStringContainsString($expectedMessage, $json['msg']);
            }
        }
    }

    /**
     * Create a validator backed by temporary validation templates and i18n files.
     *
     * @param array<string, mixed> $templateData
     * @param array<string, string> $translations
     * @param array<string, mixed> $validatorConfig
     */
    protected function makeValidatorWithResources(
        array $templateData,
        array $translations = [],
        array $validatorConfig = [],
    ): Validator
    {
        return $this->makeValidatorWithLocaleResources(
            templateDataByLocale: ['en' => $templateData],
            translationsByLocale: $translations === [] ? [] : ['en' => $translations],
            validatorConfig: $validatorConfig,
        );
    }

    /**
     * Create a validator backed by temporary validation templates and i18n files for multiple locales.
     *
     * @param array<string, array<string, mixed>> $templateDataByLocale
     * @param array<string, array<string, string>> $translationsByLocale
     * @param array<string, mixed> $validatorConfig
     */
    protected function makeValidatorWithLocaleResources(
        array $templateDataByLocale,
        array $translationsByLocale = [],
        array $validatorConfig = [],
        array $extraTemplateDataSets = [],
    ): Validator
    {
        $baseDir = sys_get_temp_dir() . '/switon-validation-' . uniqid();
        $templateDir = $baseDir . '/validation';

        mkdir($templateDir, 0755, true);

        $globMap = [];
        $templateFiles = [];
        foreach ($templateDataByLocale as $locale => $templateData) {
            $file = $templateDir . '/' . $locale . '.php';
            file_put_contents($file, "<?php\n\nreturn " . var_export($templateData, true) . ";\n");
            $templateFiles[] = $file;
        }
        $globMap[$templateDir . '/*.php'] = $templateFiles;

        $extraDirs = [];
        foreach ($extraTemplateDataSets as $index => $templateSet) {
            $extraDir = $baseDir . '/validation-extra-' . $index;
            mkdir($extraDir, 0755, true);

            $extraFiles = [];
            foreach ($templateSet as $locale => $templateData) {
                $file = $extraDir . '/' . $locale . '.php';
                file_put_contents($file, "<?php\n\nreturn " . var_export($templateData, true) . ";\n");
                $extraFiles[] = $file;
            }

            $extraDirs[] = $extraDir;
            $globMap[$extraDir . '/*.php'] = $extraFiles;
        }

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('glob')->willReturnCallback(
            static function (string $pattern) use ($globMap): array {
                return $globMap[$pattern] ?? [];
            }
        );

        $this->container->remove(FilesystemInterface::class);
        $this->container->set(FilesystemInterface::class, $filesystem);
        $this->container->remove(TranslatorInterface::class);
        $this->container->set(TranslatorInterface::class, new CatalogTranslator(
            $this->container->get(LocaleInterface::class),
            $translationsByLocale,
        ));

        return $this->container->make(Validator::class, array_merge([
            'dirs' => array_merge([$templateDir], $extraDirs),
        ], $validatorConfig));
    }

    protected function tearDown(): void
    {
        foreach (glob(sys_get_temp_dir() . '/switon-validation-*') ?: [] as $dir) {
            $this->removeDirectory($dir);
        }

        parent::tearDown();
    }

    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*') ?: [] as $path) {
            if (is_dir($path)) {
                $this->removeDirectory($path);
                continue;
            }

            unlink($path);
        }

        rmdir($dir);
    }
}
