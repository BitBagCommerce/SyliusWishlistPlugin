# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)
- [➡️ Usage](./02-usage.md)

## Installation


1. *We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.*

```bash
$ composer require bitbag/wishlist-plugin
```

2. Add plugin dependencies to your `config/bundles.php` file:
```php
// config/bundles.php

return [
    ...

    BitBag\SyliusWishlistPlugin\BitBagSyliusWishlistPlugin::class => ['all' => true],
];
```

3. Import required config in your `config/packages/_sylius.yaml` file:
```yaml
# config/packages/_sylius.yaml

imports:
    ...

    - { resource: "@BitBagSyliusWishlistPlugin/Resources/config/config.yml" }
```

4. Import routing in your `config/routes.yaml` file:

```yaml
# config/routes.yaml

bitbag_sylius_wishlist_plugin:
    resource: "@BitBagSyliusWishlistPlugin/Resources/config/routing.yml"
```

5. Clear application cache by using command:

```bash
$ bin/console cache:clear
```

6. Update your database

First, please run legacy-versioned migrations by using command:

```bash
$ bin/console doctrine:migrations:migrate
```

After migration, please create a new diff migration and run it:

```bash
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

**Note:** If you are updating this plugin from version 1.4.x you need to run:

```bash
$ bin/console doctrine:migrations:version BitBag\\SyliusWishlistPlugin\\Migrations\\Version20201029161558 --add --no-interaction
```

7. Please add plugin templates into your project:
```bash
$ cp -R vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/Product templates/bundles/SyliusShopBundle
$ cp vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/_header.html.twig templates/bundles/SyliusShopBundle
$ cp vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/_logo.html.twig templates/bundles/SyliusShopBundle
```

8. Add plugin assets to your project

We recommend you to use Webpack (Encore), for which we have prepared four different instructions on how to add this plugin's assets to your project:

- [Import webpack config](./01.1-webpack-config.md)*
- [Add entry to existing config](./01.2-webpack-entry.md)
- [Import entries in your entry.js files](./01.3-import-entry.md)
- [Your own custom config](./01.4-custom-solution.md)

<small>* Default option for plugin development</small>


However, if you are not using Webpack, here are instructions on how to add optimized and compressed assets directly to your project templates:

- [Non webpack solution](./01.5-non-webpack.md)

## Asynchronous Messenger case

In case you use asynchronous Messenger transport by default, there is a need to configure all Wishlist commands to sync transport.
You can do this by configuring the `WishlistSyncCommandInterface` interface to sync transport (as presented on code listing below). 

```yaml
# config/packages/messenger.yaml

framework:
    messenger:
        transports:
            sync: 'sync://'
    routing:
        'BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface': sync
```

All commands from the plugin implement the `WishlistSyncCommandInterface` interface, so there is no need for other configuration.

## Removing anonymous wishlists after expiration period

You can remove anonymous wishlists that have not been updated for a specified period of time. To do so, you need to add `bitbag:remove-anonymous-wishlists` Symfony console command to your cron jobs.

You can specify the expiration period in your parameters file to override the default value of 30 days:

```yaml
parameters:
    bitbag_sylius_wishlist_plugin.parameters.anonymous_wishlist_expiration_period: 30 days # Remove all anonymous wishlists that were updated more than 30 days ago.
```