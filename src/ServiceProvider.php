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
 * - register(): ValidatorInterface default wiring
 * - @switon.validator.resources: built-in validation message templates
 *
 * @see \Switon\Core\ServiceProviderInterface
 * @see \Switon\Validating\ValidatorInterface
 */
#[ResourceAlias(alias: '@switon.validator.resources', path: 'src/Templates')]
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Register the default validator implementation and bundled template directory.
     */
    public function register(ContainerInterface $container): void
    {
        $container->set(ValidatorInterface::class, [
            'class' => Validator::class,
            'dirs' => ['@switon.validator.resources'],
        ]);
    }

    /**
     * No boot-time work is required for this component.
     */
    public function boot(): void
    {
    }
}
