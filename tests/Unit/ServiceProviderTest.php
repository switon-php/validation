<?php

declare(strict_types=1);

namespace Switon\Validating\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Switon\Core\ContainerInterface;
use Switon\Core\PathAliasInterface;
use Switon\Testing\Container\Container;
use Switon\Validating\ServiceProvider;
use Switon\Validating\Validator;
use Switon\Validating\ValidatorInterface;

class ServiceProviderTest extends TestCase
{
    public function testRegisterBindsValidatorDefaults(): void
    {
        $provider = new ServiceProvider();
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('set')
            ->with(ValidatorInterface::class, [
                'class' => Validator::class,
                'dirs' => ['@switon.validator.resources'],
            ])
            ->willReturnSelf();

        $provider->register($container);
    }

    public function testContainerRegistersValidatorResourceAliasFromProviderAttribute(): void
    {
        $container = new Container();
        $pathAlias = $container->get(PathAliasInterface::class);
        $resourceRoot = $pathAlias->get('@switon.validator.resources');
        $this->assertIsString($resourceRoot);
        $this->assertStringEndsWith('/packages/validation/src/Templates', $resourceRoot);
    }
}
