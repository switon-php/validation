<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Constraint\Attribute;

use Switon\Validating\Attribute\Ip;
use Switon\Validating\Exception\ValidateFailedException;
use Switon\Validating\Tests\TestCase;

/**
 * Test cases for Ip constraint.
 *
 * Tests IP address validation (IPv4 and IPv6).
 */
#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class IpTest extends TestCase
{
    /**
     * Test Ip passes with valid IPv4 address.
     */
    public function testIpPassesWithValidIpv4Address(): void
    {
        $constraint = new Ip();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '192.168.1.1';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Ip passes with valid IPv6 address.
     */
    public function testIpPassesWithValidIpv6Address(): void
    {
        $constraint = new Ip();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

        $result = $constraint->validate($validation);

        $this->assertTrue($result);
    }

    /**
     * Test Ip fails with invalid IP address.
     */
    public function testIpFailsWithInvalidIpAddress(): void
    {
        $constraint = new Ip();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '999.999.999.999';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Ip fails with empty string.
     */
    public function testIpFailsWithEmptyString(): void
    {
        $constraint = new Ip();
        $validation = $this->validator->beginValidate([]);
        $validation->field = 'testField';
        $validation->value = '';

        $result = $constraint->validate($validation);

        $this->assertFalse($result);
    }

    /**
     * Test Ip throws exception when fails.
     */
    public function testIpThrowsExceptionWhenFails(): void
    {
        $constraint = new Ip();

        $this->expectException(ValidateFailedException::class);
        $this->validator->validateValue('testField', 'invalid-ip', [$constraint]);
    }
}

