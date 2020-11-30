<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\PhpUnit\Smoke;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

/**
 * A smoke-test that load's all public services from the DIC.
 * If this fails, we know that some services cannot be loaded.
 */
class LoadAllServicesTest extends KernelTestCase
{
    protected function setUpKernel(Request $request = null): void
    {
        assert($this instanceof KernelTestCase);

        if (static::$kernel instanceof KernelInterface) {
            $this->tearDown();
        }

        static::bootKernel();

        /** @var Container $container */
        $container = static::$kernel->getContainer();

        if (null === $request) {
            $request = new Request();
        }

        $container->set('request', $request);
    }

    protected function tearDownKernel(): void
    {
        /** @var Container $container */
        self::$container = static::$kernel->getContainer();

        static::ensureKernelShutdown();
    }

    /** @var bool */
    private $isKernelBooted = false;

    public function setUp(): void
    {
        if (!$this->isKernelBooted) {
            $this->isKernelBooted = true;

            $this->setUpKernel();

            /** @var KernelInterface $kernel */
            $kernel = static::$kernel;

            /** @var Container $container */
            $container = $kernel->getContainer();

            self::$container = $container;
        }
    }

    public function tearDown(): void
    {
        $this->tearDownKernel();
    }

    /**
     * @dataProvider dataProviderLoadService
     */
    public function testLoadService(string $serviceId): void
    {
        /* @var Container $container */
        $container = self::$container;

        /* @var string[] $allowedExceptionPrefixes */
        $allowedExceptionPrefixes = [];

        try {
            $container->get($serviceId);
        } catch (Throwable $throwable) {
            /* @var boolean $isAllowed */
            $isAllowed = false;

            // Check for exception messages from service loading.
            foreach ($allowedExceptionPrefixes as $prefix) {
                // Legacy check for exception message prefixes
                if (0 === strpos($throwable->getMessage(), $prefix)) {
                    $isAllowed = true;

                    break;
                }
            }

            // Specialised case for synthetic services.
            preg_match(
                '/The "(.*)" service is synthetic, it needs to be set at boot time before it can be used./',
                $throwable->getMessage(),
                $result
            );
            if (!empty($result)) {
                $isAllowed = true;
            }

            // If the exception message is not allowed, tell phpunit
            if (!$isAllowed) {
                throw $throwable;
            }
        }
        // Service could be loaded, dataset is fine.
        $this->assertTrue(true);
    }

    /** @return array<array<string>> */
    public function dataProviderLoadService(): array
    {
        if (!$this->isKernelBooted) {
            $this->setUp();
        }

        $servicesToTest = array_diff(self::$container->getServiceIds(), ['container.env_var_processors_locator']);

        return array_map(static function (string $serviceId): array {
            return [$serviceId];
        }, $servicesToTest);
    }
}
