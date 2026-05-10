<?php

declare(strict_types=1);

namespace Switon\Validating\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Switon\Core\FilesystemInterface;
use Switon\Core\Filesystem;
use Switon\Core\LocaleInterface;
use Switon\Core\PathAliasInterface;
use Switon\Testing\Container\Container;
use Switon\Validating\Validator;
use Switon\Validating\ValidatorInterface;

/**
 * Base test case for Validation tests.
 *
 * Provides common functionality for all Validation tests using Container (as in real applications).
 * No reflection is used - all dependencies are injected through Container's autowiring.
 *
 * @see \Switon\Testing\Container\Container
 */
abstract class TestCase extends BaseTestCase
{
    protected Container $container;
    protected ValidatorInterface $validator;
    protected LocaleInterface $locale;
    protected FilesystemInterface $filesystem;
    protected string $templateDir;

    protected function setUp(): void
    {
        parent::setUp();

        // Use pre-configured test container (FilesystemInterface is already registered but will be replaced with mock)
        $this->container = new Container();

        // Create mocks for dependencies with method configurations
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->locale->expects($this->any())->method('get')->willReturn('en');
        $this->locale->expects($this->any())->method('set')->willReturnSelf();

        $this->filesystem = $this->createMock(Filesystem::class);
        /** @var PathAliasInterface $pathAlias */
        $pathAlias = $this->container->get(PathAliasInterface::class);
        $this->templateDir = (string)$pathAlias->get('@switon.validator.resources');
        $this->filesystem->expects($this->any())
            ->method('glob')
            ->willReturn([
                $this->templateDir . '/en.php',
                $this->templateDir . '/zh-cn.php',
            ]);

        // Register dependencies in container (replace default Filesystem with mock)
        $this->container->set(LocaleInterface::class, $this->locale);
        $this->container->set(FilesystemInterface::class, $this->filesystem);

        // Create Validator instance using container with parameters for template directories
        // Container automatically handles #[Autowired] property injection
        $this->validator = $this->container->make(Validator::class, [
            'dirs' => [$this->templateDir],
        ]);
    }
}
