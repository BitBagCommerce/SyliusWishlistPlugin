<h1 align="center">
    <a href="http://bitbag.shop" target="_blank">
        <img src="https://raw.githubusercontent.com/bitbager/BitBagCommerceAssets/master/SyliusWishlistPlugin.png" />
    </a>
    <br />
    <a href="https://packagist.org/packages/bitbag/wishlist-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/bitbag/wishlist-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/wishlist-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/bitbag/wishlist-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/BitBagCommerce/SyliusWishlistPlugin" title="Build status" target="_blank">
            <img src="https://img.shields.io/travis/BitBagCommerce/SyliusWishlistPlugin/master.svg" />
        </a>
    <a href="https://scrutinizer-ci.com/g/BitBagCommerce/SyliusWishlistPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/BitBagCommerce/SyliusWishlistPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/bitbag/wishlist-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/bitbag/wishlist-plugin/downloads" />
    </a>
</h1>

## Overview

Working [Sylius](https://sylius.com/) [Elasticsearch](https://www.elastic.co/products/wishlist) integration based on [FOSElasticaBundle](https://github.com/FriendsOfSymfony/FOSElasticaBundle). The main goal of this plugin is to support filtering products by 
options, attributes, taxons, channels and name in the front product list page. It totally replaces the default Sylius `sylius_shop_product_index`
route.

What is more, the plugin has a nice Sylius-oriented architecture that allows mapping resources to the Elastic document easier. It is flexible as well,
so that you can customize the existing features for your specific business needs.   

## Demo

We created a demo app with some useful use-cases of the plugin! Visit [demo.bitbag.shop](https://demo.bitbag.shop/en_US/products-list/t-shirts) to take a look at it. 
The admin can be accessed under [demo.bitbag.shop/admin](https://demo.bitbag.shop/admin) link and `sylius: sylius` credentials.

## Installation
```bash
$ composer require bitbag/wishlist-plugin
```
    
Add plugin dependencies to your AppKernel.php file:
```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \BitBag\SyliusWishlistPlugin\BitBagSyliusWishlistPlugin(),
    ]);
}
```

Import required config in your `app/config/config.yml` file:

```yaml
# app/config/config.yml

imports:
    ...
    
    - { resource: "@BitBagSyliusWishlistPlugin/Resources/config/config.yml" }
```

Import routing **on top** of your `app/config/routing.yml` file:

```yaml

# app/config/routing.yml

bitbag_sylius_wishlist_plugin:
    resource: "@BitBagSyliusWishlistPlugin/Resources/config/routing.yml"
```

Update your database

```
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

## Usage

### Rendering the shop products list

When you go now to the `/{_locale}/products/taxon/{slug}` page, you should see a totally new set of filters. You should see something like this:

<div align="center">
    <img src="https://raw.githubusercontent.com/bitbager/BitBagCommerceAssets/master/BitBagElasticesearchProductIndex.jpg" />
</div>

You might also want to refer the horizontal menu to a new product list page. Follow below instructions to do so:

1. If you haven't done it yet, create a `_horizontalMenu.html.twig` file in `app/Resources/SyliusShopBundle/views/Taxon` directory.
2. Replace `sylius_shop_product_index` with `bitbag_sylius_wishlist_plugin_shop_list_products`.
3. Clean your cache with `bin/console cache:clear` command.
4. :tada:

### Excluding options and attributes in the filter menu

You might not want to show some specific options or attributes in the menu. You can set specific parameters for that:

```yml
parameters:
    bitbag_es_excluded_filter_options: []
    bitbag_es_excluded_filter_attributes: ['book_isbn', 'book_pages']
```

By default all options and attributes are indexed. After you change these parameters, remember to run `bin/console fo:el:po` command again
(a shortcut for `fos:elastica:populate`).

### Reindexing

By default, current indexes listen on all Doctrine events. You can override this setting for each index by overriding index definition in your `config.yml` file:

```yml
fos_elastica:
    indexes:
        bitbag_attribute_taxons:
            types:
                default:
                    persistence:
                        listener:
                            insert: true
                            update: false
                            delete: true
```

Indexes with `bitbag_shop_product`, `bitbag_attribute_taxons` and `bitbag_option_taxons` keys are available so far.

## Customization

### Available services you can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) and forms you can [extend](http://symfony.com/doc/current/form/create_form_type_extension.html)

```bash
```

### Parameters you can override in your parameters.yml(.dist) file

```yml

```

## Testing
```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install web -e test
$ bin/console doctrine:schema:create -e test
$ wishlist 
$ bin/console fos:elastica:populate -e test
$ bin/console server:run 127.0.0.1:8080 -d web -e test
$ open http://localhost:8080
$ bin/behat
$ bin/phpspec run
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.

## Support

We work on amazing eCommerce projects on top of Sylius, Pimcore and Vue JS. Need some help or additional resources for a project?
Write us an email on mikolaj.krol@bitbag.pl or visit [our website](https://bitbag.shop/)! :rocket:
