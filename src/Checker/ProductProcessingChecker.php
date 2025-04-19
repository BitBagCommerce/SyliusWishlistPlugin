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

namespace Sylius\WishlistPlugin\Checker;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;

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
