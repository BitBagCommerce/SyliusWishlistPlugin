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

namespace Sylius\WishlistPlugin\Command\Wishlist;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;

final class AddProductToSelectedWishlist implements AddProductToSelectedWishlistInterface
{
    public function __construct(
        private WishlistInterface $wishlist,
        private ProductInterface $product,
    ) {
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }
}
