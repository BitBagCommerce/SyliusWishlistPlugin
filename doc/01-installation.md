# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)
- [➡️ Usage](./02-usage.md)

## Instalation


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

5. Update your database

```bash
$ bin/console doctrine:migrations:migrate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

**Note:** If you are updating this plugin from version 1.4.x you need to run:

```bash
$ bin/console doctrine:migrations:version BitBag\\SyliusWishlistPlugin\\Migrations\\Version20201029161558 --add --no-interaction
```

6. Add assets to your project

TBD