# SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)

## Testing

```bash
composer install
cd tests/Application

yarn install
yarn dev
APP_ENV=test bin/console assets:install public
APP_ENV=test bin/console doctrine:schema:create
cd ../..
APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
open https://localhost:8080
vendor/bin/behat
vendor/bin/phpspec run
```
