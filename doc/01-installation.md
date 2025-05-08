# SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)
- [➡️ Usage](./02-usage.md)

## Installation


1. *We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.*

```bash
composer require sylius/wishlist-plugin
```

2. (optional) Add plugin dependencies to your `config/bundles.php` file:

```php
// config/bundles.php

return [
    ...

    Sylius\WishlistPlugin\SyliusWishlistPlugin::class => ['all' => true],
];
```

3. (optional) Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml
imports:
  ...
  - { resource: "@SyliusWishlistPlugin/config/config.yaml" }
```

4. (optional) Import routing in your `config/routes.yaml` file:

  ```yaml
# config/routes.yaml
sylius_wishlist_plugin:
    resource: "@SyliusWishlistPlugin/config/routes.yaml"
```

5. Create `bundles/SyliusShopBundle/product/common` directory in your project `templates` dir if it does not exist yet:

```bash
mkdir -p templates/bundles/SyliusShopBundle/product/common
```

6. Copy `@SyliusShopBundle/product/common/card.html.twig` template in your project:

```bash
cp vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/templates/product/common/card.html.twig templates/bundles/SyliusShopBundle/product/common/card.html.twig
```

7. Add the following code to the end of the `card.html.twig` file, just before latest closing `</div>` tag:

```twig
<hr>
{% include '@SyliusWishlistPlugin/common/add_to_wishlist.html.twig' %} 
```

8. Clear application cache by using command:

```bash
bin/console cache:clear
```

9. Update your database

```bash
bin/console doctrine:migrations:migrate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

10. Add plugin assets to your project

Just add to your `asssets/admin/entrypoint.js` and `assets/shop/entrypoint.js` the following line (create these files if it does not exist yet):

```javascript
import '../../vendor/sylius/wishlist-plugin/assets/entrypoint';
```

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
        'Sylius\WishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface': sync
```

All commands from the plugin implement the `WishlistSyncCommandInterface` interface, so there is no need for other configuration.
