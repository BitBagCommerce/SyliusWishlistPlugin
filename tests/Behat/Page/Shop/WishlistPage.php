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

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;

class WishlistPage extends SymfonyPage implements WishlistPageInterface
{
    /**
     * Works only with 1 wishlist
     * For case with many wishlists @see getProductElements
     */
    public function getItemsCount(): int
    {
        return (int) $this->getElement('items_count')->getText();
    }

    public function getProductElements(): int
    {
        $productElements = $this->getDocument()->findAll('css', '[data-test-wishlist-item-name]');

        return count($productElements);
    }

    public function addProductToSelectedWishlist(string $productName, string $wishlistName): void
    {
        $productElements = $this->getDocument()->findAll('named', ['link', $wishlistName]);

        /** @var NodeElement $productElement */
        foreach ($productElements as $productElement) {
            if ($productName === $productElement->getAttribute('data-product-name')) {
                $productElement->click();
            }
        }
    }

    public function selectedWishlistAction(string $action, string $wishlistName): void
    {
        $wishlists = $this->getDocument()->findAll('css', sprintf('[data-test-wishlist-wishlist-%s]', $action));

        foreach ($wishlists as $wishlist) {
            if ($wishlistName === $wishlist->getAttribute('data-wishlist-name')) {
                $wishlist->click();

                return;
            }
        }
    }

    public function getWishlistsCount(): int
    {
        $wishlists = $this->getDocument()->findAll('css', '[data-test-wishlist-wishlist]');

        return count($wishlists);
    }

    public function hasProduct(string $productName): bool
    {
        $productElements = $this->getDocument()->findAll('css', '[data-test-wishlist-item-name]');

        /** @var NodeElement $productElement */
        foreach ($productElements as $productElement) {
            if ($productName === $productElement->getText()) {
                return true;
            }
        }

        return false;
    }

    public function showChosenWishlist(string $wishlistName): void
    {
        $wishlistElements = $this->getDocument()->findAll('css', '[data-test-wishlist-wishlist]');

        /** @var NodeElement $wishlistElement */
        foreach ($wishlistElements as $wishlistElement) {
            if ($wishlistName === $wishlistElement->getAttribute('data-wishlist-name')) {
                $wishlistElement->click();
            }
        }
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

    public function removeSelectedProductsFromWishlist(): void
    {
        $this->getElement('remove_selected')->press();
    }

    public function exportToPdfSelectedProductsFromWishlist(): void
    {
        $this->getElement('export_selected_pdf')->press();
    }

    public function selectProductQuantity(string $productName, int $quantity): void
    {
        $addToCartElements = $this->getDocument()->findAll('css', '[data-test-wishlist-item-quantity] input');

        /** @var NodeElement $addToCartElement */
        foreach ($addToCartElements as $addToCartElement) {
            if ($productName === $addToCartElement->getAttribute('data-product-name')) {
                /** @phpstan-ignore-next-line  */
                $addToCartElement->setValue($quantity);
            }
        }
    }

    public function addProductToCart(): void
    {
        $this->getElement('add')->press();
    }

    public function addSelectedProductsToCart(): void
    {
        $this->getElement('add_selected')->press();
    }

    public function copySelectedProducts(string $wishlistName): void
    {
        $copyElements = $this->getDocument()->findAll('css', '[wishlist-copy-to-wishlist]');

        /** @var NodeElement $copyElement */
        foreach ($copyElements as $copyElement) {
            if ($wishlistName === $copyElement->getAttribute('data-wishlist-name')) {
                $copyElement->click();

                return;
            }
        }
    }

    public function exportSelectedProductsToCsv(): void
    {
        $this->getElement('export_selected_csv')->press();
    }

    public function hasProductInCart(string $productName): bool
    {
        /** @var ?NodeElement $productNameElement */
        $productNameElement = $this->getDocument()->find('css', '.ui.cart.popup > .list > .item > strong');

        if (null === $productNameElement) {
            return false;
        }

        $productNameOnPage = $productNameElement->getText();

        return $productName === $productNameOnPage;
    }

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $outOfStockValidationErrorElement = $this->getDocument()->find('css', '.sylius-flash-message p');

        if (null === $outOfStockValidationErrorElement) {
            return false;
        }

        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        return $outOfStockValidationErrorElement->getText() === $message;
    }

    public function hasWishlistClearedValidationMessage(): bool
    {
        $hasWishlistClearedValidationMessage = $this->getDocument()->find('css', '.sylius-flash-message p');

        if (null === $hasWishlistClearedValidationMessage) {
            return false;
        }

        $message = 'Wishlist has been cleared.';

        return $hasWishlistClearedValidationMessage->getText() === $message;
    }

    public function addMoreProductsWishlistValidationMessage(): bool
    {
        $notEnoughQuantityOfItemsValidationError = $this->getDocument()->find('css', '.sylius-flash-message p');

        if (null === $notEnoughQuantityOfItemsValidationError) {
            return false;
        }

        $message = sprintf('Increase the quantity of at least one item.');

        return $notEnoughQuantityOfItemsValidationError->getText() === $message;
    }

    public function getRouteName(): string
    {
        return 'sylius_wishlist_plugin_shop_wishlist_list_products';
    }

    protected function getDefinedElements(): array
    {
        return [
            'add' => '[data-test-wishlist-add-all-to-cart]',
            'add_selected' => '[data-test-wishlist-add-selected-to-cart]',
            'remove_selected' => '[data-test-wishlist-remove-selected-from-wishlist]',
            'items_count' => '[data-test-wishlist-primary-items-count]',
            'export_selected_csv' => '[data-test-wishlist-export-to-csv]',
            'export_selected_pdf' => '[data-test-wishlist-export-to-pdf-from-wishlist]',
        ];
    }

    public function waitForOneSecond(): void
    {
        $this->getDriver()->wait(1000, 'false == true');
    }
}
