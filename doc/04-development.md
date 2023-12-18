# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)
- [➡️ Testing](./05-testing.md)

## Plugin Development

- [Installation](#installation)
- [Development](#development)
- [Frontend](#frontend)


#### Installation

Clone this repository, go to the plugin root directory and run

```bash
$ composer install
$ cd tests/Application
```

If needed, create `.env.local` file with the correct configuration for your environment in the `tests/Application` directory. <br>
Then run the following commands from `tests/Application`:

```bash
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:create
$ bin/console sylius:fixtures:load
$ bin/console assets:install --symlink
```

Then:

```bash
$ yarn install
$ yarn dev
```

You're ready to start coding 🎉

#### Development

To start the development server, from the `tests/Application` directory run:

```bash
$ symfony server:start
```

and then you should get information about the server address and port (usually http://localhost:8000). <br>
If you don't already have Symfony CLI, here's how to install it: https://symfony.com/download


#### Frontend

To start working on frontend, from the `tests/Application` directory run:

```bash
$ yarn watch
```

It's an infinite process, which will watch your changes in the assets folder and (re)build them. So all of your frontend changes should be done in `{root}/src/Resources/assets` directory. We have configured two independent entry points that should not be combined - `shop` for the storefront and `admin` for the admin panel.

> **⚠ Note**: Before every commit, you should type the `yarn dist` command from the plugin root directory to rebuild dist assets, which are located in `{root}/src/Resources/public`. <br> <br> You also shouldn't add assets to this folder manually because **they will be removed automatically**
