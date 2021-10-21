<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;

class WishlistPage extends SymfonyPage implements WishlistPageInterface
{
    public function getItemsCount(): int
    {
        return (int) $this->getDocument()->find('css', '[data-test-wishlist-primary-items-count]')->getText();
    }

    public function hasProduct(string $productName): bool
    {
        $productElements = $this->getDocument()->findAll('css', '[data-test-wishlisst-item-name]');

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
        $wishlistElements = $this->getDocument()->findAll('css', '[data-test-wishlist-remove-item]');

        /** @var NodeElement $wishlistElement */
        foreach ($wishlistElements as $wishlistElement) {
            if ($productName === $wishlistElement->getAttribute('data-product-name')) {
                $wishlistElement->click();
            }
        }
    }

    public function selectProductQuantity(string $productName, int $quantity): void
    {
        $addToCartElements = $this->getDocument()->findAll('css', '[data-test-wishlist-item-quantity] input');

        /** @var NodeElement $addToCartElement */
        foreach ($addToCartElements as $addToCartElement) {
            if ($productName === $addToCartElement->getAttribute('data-product-name')) {
                $addToCartElement->setValue($quantity);
            }
        }
    }

    public function addProductToCart(): void
    {
        $this->getDocument()->find('css', '[data-test-wishlist-add-all-to-cart]')->press();
    }

    public function addSelectedProductsToCart(): void
    {
        $this->getDocument()->find('css', '.bitbag-action')->press();
    }

    public function hasProductInCart(string $productName): bool
    {
        $productNameOnPage = $this->getDocument()->find('css', '.ui.cart.popup > .list > .item > strong')->getText();

        return $productName === $productNameOnPage;
    }

    public function hasProductOutOfStockValidationMessage(ProductInterface $product)
    {
        $outOfStockValidationErrorElement = $this->getDocument()->find('css', '.sylius-flash-message p');

        if (null === $outOfStockValidationErrorElement) {
            return false;
        }

        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        return $outOfStockValidationErrorElement->getText() === $message;
    }

    public function getRouteName(): string
    {
        return 'bitbag_sylius_wishlist_plugin_shop_wishlist_list_products';
    }
}
