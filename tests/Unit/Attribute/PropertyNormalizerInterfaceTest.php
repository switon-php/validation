<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Attribute;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use ReflectionProperty;
use Switon\Validating\Attribute\PropertyNormalizerInterface;
use Switon\Validating\Tests\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class PropertyNormalizerInterfaceTest extends TestCase
{
    public function testInterfaceCanBeImplemented(): void
    {
        $input = new class {
            public string $status = '';
        };
        $normalizer = new class implements PropertyNormalizerInterface {
            public function normalizeInput(ReflectionProperty $property, mixed $value): mixed
            {
                return strtoupper((string)$value);
            }
        };

        $property = new ReflectionProperty($input, 'status');

        self::assertSame('PAID', $normalizer->normalizeInput($property, 'paid'));
    }
}
