# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)

## Testing

```bash
composer install
cd tests/Application
```

Copy `package.json.~1.xx.dist` file to `package.json` for specific version of Sylius (example for 1.12.0):
```bash
cp package.json.\~1.12.dist package.json
```

Then:

```bash
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
