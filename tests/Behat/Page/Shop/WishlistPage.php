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

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class WishlistPage extends SymfonyPage implements WishlistPageInterface
{
    public function getItemsCount(): int
    {
        return (int) $this->getDocument()->find('css', '.bitbag-wishlist-items-count')->getText();
    }

    public function hasProduct(string $productName): bool
    {
        $productElements = $this->getDocument()->findAll('css', '.bitbag-wishlist-item .sylius-product-name');

        /** @var NodeElement $productElement */
        foreach ($productElements as $productElement) {
            if ($productName === $productElement->getText()) {
                return true;
            }
        }

        return false;
    }

    public function removeProduct(string $productName): void
    {
        $wishlistElements = $this->getDocument()->findAll('css', '.bitbag-remove-from-wishlist');

        /** @var NodeElement $wishlistElement */
        foreach ($wishlistElements as $wishlistElement) {
            if ($productName === $wishlistElement->getAttribute('data-product-name')) {
                $wishlistElement->click();
            }
        }
    }

    public function selectProductQuantity(string $productName, int $quantity): void
    {
        $addToCartElements = $this->getDocument()->findAll('css', '.bitbag-wishlist-add-item-to-cart');

        /** @var NodeElement $addToCartElement */
        foreach ($addToCartElements as $addToCartElement) {
            if ($productName === $addToCartElement->getAttribute('data-product-name')) {
                $addToCartElement->setValue($quantity);
            }
        }
    }

    public function addProductToCart(): void
    {
        $this->getDocument()->find('css', '.bitbag-add-products-to-wishlist')->press();
    }

    public function hasProductInCart(string $productName): bool
    {
        $productNameOnPage = $this->getDocument()->find('css', '.ui.cart.popup > .list > .item > strong')->getText();

        return $productName === $productNameOnPage;
    }

    public function getRouteName(): string
    {
        return 'bitbag_sylius_wishlist_plugin_shop_wishlist_list_products';
    }
}
