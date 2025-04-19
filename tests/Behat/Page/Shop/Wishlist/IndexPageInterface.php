<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
