# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)

## Testing

```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn prod
$ bin/console assets:install public -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ cd ../..
$ vendor/bin/behat
$ vendor/bin/phpspec run
```