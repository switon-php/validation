<?php

declare(strict_types=1);

/**
 * Validation message templates (English).
 *
 * @see \Switon\Validating\Validator Message source
 * @see \Switon\Validating\Exception\LocaleTemplateNotFoundException Related failure path
 */
return [
    'default' => 'The {field} is invalid.',
    'Switon\Validating\Attribute\Type' => 'The {field} data type is not {type}.',
    'Switon\Validating\Attribute\Required' => 'The {field} field is required.',
    'Switon\Validating\Attribute\NotEmpty' => 'The {field} must not be empty.',
    'Switon\Validating\Attribute\Date' => 'The {field} is not a valid date.',
    'Switon\Validating\Attribute\Range' => 'The {field} must be between {min} and {max}.',
    'Switon\Validating\Attribute\Min' => 'The {field} must be not less than {min}.',
    'Switon\Validating\Attribute\Max' => 'The {field} must be not greater than {max}.',
    'Switon\Validating\Attribute\MinLength' => 'The {field} must be at least {min} characters.',
    'Switon\Validating\Attribute\MaxLength' => 'The {field} must be shorter than {max} characters.',
    'Switon\Validating\Attribute\Length' => 'The {field} must be between {min} and {max} characters.',
    'Switon\Validating\Attribute\Alpha' => 'The {field} may only contain letters.',
    'Switon\Validating\Attribute\Digit' => 'The {field} must be digits.',
    'Switon\Validating\Attribute\Alnum' => 'The {field} may only contain letters and numbers.',
    'Switon\Validating\Attribute\Xdigit' => 'The {field} must be a valid hexadecimal string.',
    'Switon\Validating\Attribute\Email' => 'The {field} must be a valid email address.',
    'Switon\Validating\Attribute\Ip' => 'The {field} must be a valid IP address.',
    'Switon\Validating\Attribute\Url' => 'The {field} must be a valid URL.',
    'Switon\Validating\Attribute\Uuid' => 'The {field} is not a valid UUID.',
    'Switon\Validating\Attribute\Mobile' => 'The {field} must be a valid mobile number.',
    'Switon\Validating\Attribute\Account' => 'The {field} must start with a letter and contain only lowercase letters, digits and underscores.',
    'Switon\Validating\Attribute\Regex' => 'The {field} format is invalid.',
    'Switon\Validating\Attribute\In' => 'The {field} must be one of {values}.',
    'Switon\Validating\Attribute\NotIn' => 'The {field} must not be one of {values}.',
    'Switon\Validating\Attribute\ConstantValue' => 'The {field} must be one of the allowed values.',
    'Switon\Validating\Attribute\Decimal' => 'The {field} must be a valid decimal number.',
    'Switon\Validating\Attribute\StartsWith' => 'The {field} must start with {needles}.',
    'Switon\Validating\Attribute\EndsWith' => 'The {field} must end with {needles}.',
    'Switon\Validating\Attribute\EqualTo' => 'The {field} must be equal to {otherField}.',
    'Switon\Validating\Attribute\Lowercase' => 'The {field} must be lowercase.',
    'Switon\Validating\Attribute\Uppercase' => 'The {field} must be uppercase.',
    'Switon\Orm\Attribute\Unique' => 'The {entity} already exists with the same {fields}.',
    'Switon\Orm\Attribute\Immutable' => 'The {field} can not be modified.',
];
