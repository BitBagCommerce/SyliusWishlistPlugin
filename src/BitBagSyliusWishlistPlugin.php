<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin;

use Sylius\WishlistPlugin\DependencyInjection\SyliusMessageBusPolyfillPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BitBagSyliusWishlistPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getContainerExtension(): ?ExtensionInterface
    {
        $this->containerExtension = $this->createContainerExtension() ?? false;

        return false !== $this->containerExtension ? $this->containerExtension : null;
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            new SyliusMessageBusPolyfillPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1,
        );
    }
}
