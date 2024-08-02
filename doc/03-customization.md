# BitBag SyliusWishlistPlugin

- [⬅️ Back](../README.md#overview)
- [➡️ Development](./04-development.md)

## Customization

### List of available services you can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) and forms you can [extend](http://symfony.com/doc/current/form/create_form_type_extension.html)

Run the below command to see what Symfony services are shared with this plugin:
```bash
bin/console debug:container | grep bitbag_sylius_wishlist_plugin
```

### List of parameters you can override in your parameters.yml(.dist) file
```bash
bin/console debug:container --parameters | grep bitbag
bin/console debug:container --parameters | grep wishlist
```
