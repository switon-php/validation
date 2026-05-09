<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit\Attribute;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use ReflectionProperty;
use Switon\Core\Clock;
use Switon\Validating\Attribute\ArrayOf;
use Switon\Validating\Tests\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class ArrayOfTest extends TestCase
{
    public function testAttributeCapturesTypeAndBounds(): void
    {
        $ref = new ReflectionProperty(ArrayOfSample::class, 'bounded');
        $attrs = $ref->getAttributes(ArrayOf::class);
        $this->assertCount(1, $attrs);

        $arrayOf = $attrs[0]->newInstance();
        $this->assertInstanceOf(ArrayOf::class, $arrayOf);
        $this->assertSame('int', $arrayOf->type);
        $this->assertSame(1, $arrayOf->minItems);
        $this->assertSame(10, $arrayOf->maxItems);
    }

    public function testAttributeWithTypeOnlyLeavesBoundsNull(): void
    {
        $ref = new ReflectionProperty(ArrayOfSample::class, 'simple');
        $attrs = $ref->getAttributes(ArrayOf::class);
        $this->assertCount(1, $attrs);

        $arrayOf = $attrs[0]->newInstance();
        $this->assertSame(Clock::class, $arrayOf->type);
        $this->assertNull($arrayOf->minItems);
        $this->assertNull($arrayOf->maxItems);
    }
}

final class ArrayOfSample
{
    #[ArrayOf('int', 1, 10)]
    public array $bounded = [];

    #[ArrayOf(Clock::class)]
    public array $simple = [];
}
