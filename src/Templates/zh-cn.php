<?php

declare(strict_types=1);

/**
 * Validation message templates (Simplified Chinese).
 *
 * @see \Switon\Validating\Validator Message source
 * @see \Switon\Validating\Exception\LocaleTemplateNotFoundException Related failure path
 */
return [
    'default' => '{field} 值无效',
    'Switon\Validating\Attribute\Type' => '{field} 数据类型不正确',
    'Switon\Validating\Attribute\Required' => '{field} 是必填项',
    'Switon\Validating\Attribute\NotEmpty' => '{field} 不能为空',
    'Switon\Validating\Attribute\Date' => '{field} 日期格式错误',
    'Switon\Validating\Attribute\Range' => '{field} 有效范围为: {min} ~ {max}',
    'Switon\Validating\Attribute\Min' => '{field} 不能小于 {min}',
    'Switon\Validating\Attribute\Max' => '{field} 不能大于 {max}',
    'Switon\Validating\Attribute\MinLength' => '{field} 最少 {min} 个字符',
    'Switon\Validating\Attribute\MaxLength' => '{field} 最多 {max} 个字符',
    'Switon\Validating\Attribute\Length' => '{field} 长度有效范围为: {min} ~ {max} 个字符',
    'Switon\Validating\Attribute\Alpha' => '{field} 只能包含字母',
    'Switon\Validating\Attribute\Digit' => '{field} 只能包含数字',
    'Switon\Validating\Attribute\Alnum' => '{field} 只能包含字母或数字',
    'Switon\Validating\Attribute\Xdigit' => '{field} 必须是有效的十六进制字符串',
    'Switon\Validating\Attribute\Email' => '{field} 邮件格式错误',
    'Switon\Validating\Attribute\Ip' => '{field} IP地址格式无效',
    'Switon\Validating\Attribute\Url' => '{field} 必须是有效的URL',
    'Switon\Validating\Attribute\Uuid' => '{field} 不是有效的UUID',
    'Switon\Validating\Attribute\Mobile' => '{field} 手机号格式无效',
    'Switon\Validating\Attribute\Account' => '{field} 格式无效，需以小写字母开头，只能包含小写字母、数字和下划线',
    'Switon\Validating\Attribute\Regex' => '{field} 格式无效',
    'Switon\Validating\Attribute\In' => '{field} 必须是 {values} 之一',
    'Switon\Validating\Attribute\NotIn' => '{field} 不能是 {values} 之一',
    'Switon\Validating\Attribute\ConstantValue' => '{field} 必须是允许的值之一',
    'Switon\Validating\Attribute\Decimal' => '{field} 必须是有效的十进制数',
    'Switon\Validating\Attribute\StartsWith' => '{field} 必须以 {needles} 开头',
    'Switon\Validating\Attribute\EndsWith' => '{field} 必须以 {needles} 结尾',
    'Switon\Validating\Attribute\EqualTo' => '{field} 必须与 {otherField} 相同',
    'Switon\Validating\Attribute\Lowercase' => '{field} 必须是小写',
    'Switon\Validating\Attribute\Uppercase' => '{field} 必须是大写',
    'Switon\Orm\Attribute\Unique' => '{entity} 已存在相同的 {fields}，请勿重复创建',
    'Switon\Orm\Attribute\Immutable' => '不能修改 {field} 字段的值',
];
