# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
FRONTEND
- [Templates](#templates)
- [Webpack](#webpack)
---
ADDITIONAL
- [Additional configuration](#additional-configuration)
- [Known Issues](#known-issues)
---

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>=8.1          |
| sylius/sylius | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | \>= 18.x        |

## Composer:
```bash
composer require bitbag/wishlist-plugin
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    BitBag\SyliusWishlistPlugin\BitBagSyliusWishlistPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    - { resource: "@BitBagSyliusWishlistPlugin/Resources/config/config.yml" }
```

Import routing in your `config/routes.yaml` file:
```yaml
# config/routes.yaml

bitbag_sylius_wishlist_plugin:
    resource: "@BitBagSyliusWishlistPlugin/Resources/config/routing.yml"
```

### Update your database
First, please run legacy-versioned migrations by using command:
```bash
bin/console doctrine:migrations:migrate
```

After migration, please create a new diff migration and update database:
```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

### Clear application cache by using command:
```bash
bin/console cache:clear
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

**Note:** If you are updating this plugin from version `1.4.x` you need to run:

```bash
bin/console doctrine:migrations:version BitBag\\SyliusWishlistPlugin\\Migrations\\Version20201029161558 --add --no-interaction
```

## Templates
Copy required templates into correct directories in your project.

ShopBundle (`templates/bundles/SyliusShopBundle`):
```
vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/Product/_box.html.twig
vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/Product/Show/_addToCart.html.twig
vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/_header.html.twig
vendor/bitbag/wishlist-plugin/tests/Application/templates/bundles/SyliusShopBundle/_logo.html.twig
```

## Webpack
### Webpack.config.js

Please setup your `webpack.config.js` file to require the plugin's webpack configuration. To do so, please put the line below somewhere on top of your webpack.config.js file:
```js
const [ bitbagWishlistShop, bitbagWishlistAdmin ] = require('./vendor/bitbag/wishlist-plugin/webpack.config.js')
```
As next step, please add the imported consts into final module exports:
```js
module.exports = [..., bitbagWishlistShop, bitbagWishlistAdmin];
```

### Assets
Add the asset configuration into `config/packages/assets.yaml`:
```yaml
framework:
    assets:
        packages:
            ...
            wishlist_shop:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/wishlist/shop/manifest.json'
            wishlist_admin:
                json_manifest_path: '%kernel.project_dir%/public/build/bitbag/wishlist/admin/manifest.json'
```

### Webpack Encore
Add the webpack configuration into `config/packages/webpack_encore.yaml`:

```yaml
webpack_encore:
    output_path: '%kernel.project_dir%/public/build/default'
    builds:
        ...
        wishlist_shop: '%kernel.project_dir%/public/build/bitbag/wishlist/shop'
        wishlist_admin: '%kernel.project_dir%/public/build/bitbag/wishlist/admin'
```

### Encore functions
Add encore functions to your templates:

SyliusAdminBundle:
```php
{# @SyliusAdminBundle/_scripts.html.twig #}
{{ encore_entry_script_tags('bitbag-wishlist-admin', null, 'wishlist_admin') }}

{# @SyliusAdminBundle/_styles.html.twig #}
{{ encore_entry_link_tags('bitbag-wishlist-admin', null, 'wishlist_admin') }}
```
SyliusShopBundle:
```php
{# @SyliusShopBundle/_scripts.html.twig #}
{{ encore_entry_script_tags('bitbag-wishlist-shop', null, 'wishlist_shop') }}

{# @SyliusShopBundle/_styles.html.twig #}
{{ encore_entry_link_tags('bitbag-wishlist-shop', null, 'wishlist_shop') }}
```

### Run commands
```bash
yarn install
yarn encore dev # or prod, depends on your environment
```

## Additional configuration
### Asynchronous Messenger case
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

## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
