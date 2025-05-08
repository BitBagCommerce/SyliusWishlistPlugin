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

namespace Tests\Sylius\WishlistPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface WishlistPageInterface extends SymfonyPageInterface
{
    public function getItemsCount(): int;

    public function getProductElements(): int;

    public function addProductToSelectedWishlist(string $productName, string $wishlistName): void;

    public function selectedWishlistAction(string $action, string $wishlistName): void;

    public function getWishlistsCount(): int;

    public function hasProduct(string $productName): bool;

    public function showChosenWishlist(string $wishlistName): void;

    public function removeProduct(string $productName): void;

    public function removeSelectedProductsFromWishlist(): void;

    public function exportToPdfSelectedProductsFromWishlist(): void;

    public function selectProductQuantity(string $productName, int $quantity): void;

    public function addProductToCart(): void;

    public function addSelectedProductsToCart(): void;

    public function copySelectedProducts(string $wishlistName): void;

    public function exportSelectedProductsToCsv(): void;

    public function hasProductInCart(string $productName): bool;

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool;

    public function hasWishlistClearedValidationMessage(): bool;

    public function addMoreProductsWishlistValidationMessage(): bool;

    public function waitForOneSecond(): void;
}
