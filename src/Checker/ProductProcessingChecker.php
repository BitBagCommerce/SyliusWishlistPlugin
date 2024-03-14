<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;

final class ProductProcessingChecker implements ProductProcessingCheckerInterface
{
    private ProductQuantityCheckerInterface $productQuantityChecker;

    public function __construct(
        ProductQuantityCheckerInterface $productQuantityChecker
    ) {
        $this->productQuantityChecker = $productQuantityChecker;
    }

    public function canBeProcessed(WishlistItem $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        return $this->isInStock($wishlistProduct) && $this->productQuantityChecker->hasPositiveQuantity($cartItem);
    }

    private function isInStock(WishlistItem $wishlistProduct): bool
    {
        if (0 < $wishlistProduct->getOrderItemQuantity()) {
            return true;
        }

        return false;
    }
}
