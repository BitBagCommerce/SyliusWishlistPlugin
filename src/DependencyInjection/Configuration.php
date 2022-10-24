<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\DependencyInjection;

use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bitbag_sylius_wishlist_plugin');
        $rootNode = $treeBuilder->getRootNode();
        /** @phpstan-ignore-next-line  */
        $rootNode
            ->children()
                ->scalarNode('wishlist_cookie_token')
                    ->defaultValue('wishlist_cookie_token')
                    ->cannotBeEmpty()
                    ->validate()
                        ->always(function ($value) {
                            if (!is_string($value)) {
                                throw new InvalidConfigurationException('wishlist_cookie_token must be string');
                            }

                            return $value;
                        })
                    ->end()
                ->end()
                ->arrayNode('allowed_mime_types')
                    ->defaultValue([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/comma-separated-values',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'text/anytext',
                        'application/octet-stream',
                        'application/txt',
                    ])
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('wishlist')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Wishlist::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(WishlistInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(WishlistRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('wishlist_product')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(WishlistProduct::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(WishlistProductInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(EntityRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('wishlist_expiration_period')
                    ->defaultValue('1 month')
                    ->cannotBeEmpty()
                    ->info('Period for which delete old wishlists without products')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
