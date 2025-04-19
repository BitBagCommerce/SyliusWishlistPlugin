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

final class AddProductToWishlist implements WishlistTokenValueAwareInterface
{
    private WishlistInterface $wishlist;

    public function __construct(public int $productId)
    {
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }

    public function setWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlist = $wishlist;
    }
}
