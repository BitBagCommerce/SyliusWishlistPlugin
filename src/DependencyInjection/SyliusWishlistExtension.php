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

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusWishlistExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
        $container->setParameter('sylius_wishlist_plugin.parameters.wishlist_cookie_token', $config['wishlist_cookie_token']);
        $container->setParameter('sylius_wishlist_plugin.parameters.allowed_mime_types', $config['allowed_mime_types']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        trigger_deprecation('bitbag/wishlist-plugin', '2.0', 'Doctrine migrations existing in a bundle will be removed, move migrations to the project directory.');
        $this->prependDoctrineMigrations($container);

        $config = $this->getCurrentConfiguration($container);
        $this->registerResources('sylius_wishlist_plugin', 'doctrine/orm', $config['resources'], $container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Sylius\WishlistPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusWishlistPlugin/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return ['Sylius\Bundle\CoreBundle\Migrations'];
    }

    private function getCurrentConfiguration(ContainerBuilder $container): array
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);
        $configs = $container->getExtensionConfig($this->getAlias());

        return $this->processConfiguration($configuration, $configs);
    }
}
