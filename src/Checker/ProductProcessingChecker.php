<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Checker;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;

final class ProductProcessingChecker implements ProductProcessingCheckerInterface
{
    public function canBeProcessed(WishlistItemInterface $wishlistItem): bool
    {
        /** @var ?AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $wishlistItem->getCartItem();

        if (null === $addToCartCommand) {
            return false;
        }

        $cartItem = $addToCartCommand->getCartItem();

        return 0 < $cartItem->getQuantity();
    }
}
