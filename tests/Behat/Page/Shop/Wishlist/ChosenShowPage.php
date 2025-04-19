<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
