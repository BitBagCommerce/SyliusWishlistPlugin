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
