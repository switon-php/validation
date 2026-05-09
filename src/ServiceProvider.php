<?php

declare(strict_types=1);

namespace Switon\Validating;

use Switon\Core\Attribute\ResourceAlias;
use Switon\Core\ContainerInterface;
use Switon\Core\ServiceProviderInterface;

/**
 * Registers validator defaults and package resource aliases.
 *
 * Road-signs:
 * - ValidatorInterface defaults
 * - @switon.validator.resources
 *
 * @see \Switon\Core\ServiceProviderInterface
 * @see \Switon\Validating\ValidatorInterface
 */
#[ResourceAlias(path: 'src/Templates', alias: '@switon.validator.resources')]
class ServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->set(ValidatorInterface::class, [
            'class' => Validator::class,
            'dirs' => ['@switon.validator.resources'],
        ]);
    }

    public function boot(): void
    {
    }
}
