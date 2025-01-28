<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;

final readonly class ProductProcessingChecker implements ProductProcessingCheckerInterface
{
    public function __construct(
        private ProductQuantityCheckerInterface $productQuantityChecker,
    ) {
    }

    public function canBeProcessed(WishlistItemInterface $wishlistItem): bool
    {
        /** @var ?AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $wishlistItem->getCartItem();

        if (null === $addToCartCommand) {
            return false;
        }

        $cartItem = $addToCartCommand->getCartItem();

        return $this->isInStock($wishlistItem) && $this->productQuantityChecker->hasPositiveQuantity($cartItem);
    }

    private function isInStock(WishlistItemInterface $wishlistItem): bool
    {
        if (0 < $wishlistItem->getOrderItemQuantity()) {
            return true;
        }

        return false;
    }
}
