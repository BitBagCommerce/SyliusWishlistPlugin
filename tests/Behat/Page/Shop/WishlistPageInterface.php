<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

interface WishlistPageInterface extends SymfonyPageInterface
{
    public function getItemsCount(): int;

    public function hasProduct(string $productName): bool;

    public function cleanWishlist(int $wishlistId): void;

    public function removeProduct(string $productName): void;

    public function selectProductQuantity(string $productName, int $quantity): void;

    public function addProductToCart(): void;

    public function hasProductInCart(string $productName): bool;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product);
}
