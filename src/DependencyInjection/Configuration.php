<?php

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

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bitbag_sylius_wishlist_plugin');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
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
            ->scalarNode('repository')->defaultValue(WishlistRepository::class)->cannotBeEmpty()->end()
            ->scalarNode('controller')->defaultValue(EntityRepository::class)->cannotBeEmpty()->end()
            ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
