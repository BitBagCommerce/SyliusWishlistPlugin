<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\WishlistPlugin\DependencyInjection\SyliusMessageBusPolyfillPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusWishlistPlugin extends Bundle
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
