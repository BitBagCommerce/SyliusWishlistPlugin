<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\Sylius\WishlistPlugin\Behat\Page\Shop\Wishlist;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class ChosenShowPage extends SymfonyPage implements ChosenShowPageInterface
{
    public function getRouteName(): string
    {
        return 'bitbag_sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist';
    }
}
