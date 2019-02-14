<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface WishlistPageInterface extends SymfonyPageInterface
{
    public function getItemsCount(): int;

    public function hasProduct(string $productName): bool;

    public function removeProduct(string $productName): void;

    public function selectProductQuantity(string $productName, int $quantity): void;

    public function addProductToCart(): void;

    public function hasProductInCart(string $productName): bool;
}
