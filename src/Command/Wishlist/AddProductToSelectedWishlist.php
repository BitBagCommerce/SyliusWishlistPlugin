<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Command\Wishlist;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ProductInterface;

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
