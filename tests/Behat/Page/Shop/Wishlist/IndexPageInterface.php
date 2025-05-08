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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    public function addNewWishlist(): void;

    public function fillNewWishlistName(string $name): void;

    public function saveNewWishlist(): void;

    public function editWishlistName(string $wishlistName): void;

    public function fillEditWishlistName(string $newWishlistName): void;

    public function saveEditWishlist(): void;
}
