<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusMessageBusPolyfillPass implements CompilerPassInterface
{
    public const ID_FALLBACK = [
        'sylius.command_bus' => 'sylius_default.bus'
    ];

    public const COMMAND_BUS_ALIAS = 'bitbag.sylius_wishlist_plugin.command_bus';

    private function setupDefaultCommandBus(array $buses, ContainerBuilder $container): void
    {
        $targetBusName = in_array('sylius.command_bus', $buses, true) ? 'sylius.command_bus' : 'sylius_default.bus';
        $container->setAlias(
            SyliusMessageBusPolyfillPass::COMMAND_BUS_ALIAS,
            $targetBusName
        );
    }

    public function process(ContainerBuilder $container): void
    {
        $buses = array_keys($container->findTaggedServiceIds('messenger.bus'));
        $this->setupDefaultCommandBus($buses, $container);
    }
}
