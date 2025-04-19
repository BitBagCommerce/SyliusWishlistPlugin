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

namespace Sylius\WishlistPlugin\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusMessageBusPolyfillPass implements CompilerPassInterface
{
    public const TAG_FALLBACK = [
        'sylius.command_bus' => 'sylius_default.bus',
        'sylius.event_bus' => 'sylius_event.bus',
    ];

    public const COMMAND_BUS_TAG = 'bitbag.sylius_wishlist_plugin.command_bus';

    private function setupDefaultCommandBus(array $buses, ContainerBuilder $container): void
    {
        $targetBusName = in_array('sylius.command_bus', $buses, true) ? 'sylius.command_bus' : 'sylius_default.bus';
        $container->setAlias(
            self::COMMAND_BUS_TAG,
            $targetBusName,
        );
    }

    public function process(ContainerBuilder $container): void
    {
        /**
         * @var array<string, array> $handlers
         */
        $handlers = $container->findTaggedServiceIds(self::COMMAND_BUS_TAG);
        $buses = array_keys($container->findTaggedServiceIds('messenger.bus'));
        $this->setupDefaultCommandBus($buses, $container);

        foreach ($handlers as $handler => $tagData) {
            if (!isset($tagData[0]['bus'])) {
                continue;
            }

            $busName = (string) $tagData[0]['bus'];

            $def = $container->findDefinition($handler);
            $def->addTag('messenger.message_handler', [
                'bus' => in_array($busName, $buses, true) ? $busName : self::TAG_FALLBACK[$busName],
            ]);
        }
    }
}
