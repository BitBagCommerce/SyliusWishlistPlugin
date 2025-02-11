# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)
- [➡️ Usage](./02-usage.md)

## Installation


1. *We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.*

```bash
composer require bitbag/wishlist-plugin
```

2. (optional) Add plugin dependencies to your `config/bundles.php` file:

```php
// config/bundles.php

return [
    ...

    BitBag\SyliusWishlistPlugin\BitBagSyliusWishlistPlugin::class => ['all' => true],
];
```

3. (optional) Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml
imports:
  ...
  - { resource: "@BitBagSyliusWishlistPlugin/Resources/config/config.yml" }
```

4. (optional) Import routing in your `config/routes.yaml` file:

  ```yaml
# config/routes.yaml
bitbag_sylius_wishlist_plugin:
    resource: "@BitBagSyliusWishlistPlugin/Resources/config/routing.yml"
```

5. Override `OrderItemController`

```yaml
sylius_order:
  resources:
    order_item:
      classes:
        controller: BitBag\SyliusWishlistPlugin\Controller\OrderItemController

```

6. Add plugin templates:

- Inject blocks:

```yaml
sylius_ui:
  events:
    sylius.shop.layout.header.grid:
      blocks:
        wishlist_header: '@BitBagSyliusWishlistPlugin/_wishlist_header.html.twig'
    sylius.shop.product.index.box:
      blocks:
        content:
          template: "@BitBagSyliusWishlistPlugin/Product/Box/_content.html.twig"
          priority: 10
        
```

- Override templates:

```bash
cp vendor/bitbag/wishlist-plugin/src/Resources/views/Product/Show/_addToCart.html.twig templates/bundles/SyliusShopBundle/Product/Show
```

7. Clear application cache by using command:

```bash
bin/console cache:clear
```

8. Update your database

```bash
bin/console doctrine:migrations:migrate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

9. Add plugin assets to your project

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
