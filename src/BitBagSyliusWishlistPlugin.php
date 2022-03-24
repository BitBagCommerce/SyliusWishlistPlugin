<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin;

use BitBag\SyliusWishlistPlugin\DependencyInjection\SyliusMessageBusPolyfillPass;
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
            1
        );
    }
}
