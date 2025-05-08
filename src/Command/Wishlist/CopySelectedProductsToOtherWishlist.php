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

use Doctrine\Common\Collections\Collection;

final class CopySelectedProductsToOtherWishlist implements CopySelectedProductsToOtherWishlistInterface
{
    public function __construct(
        private Collection $wishlistProducts,
        private int $destinedWishlistId,
    ) {
    }

    public function getWishlistProducts(): Collection
    {
        return $this->wishlistProducts;
    }

    public function getDestinedWishlistId(): int
    {
        return $this->destinedWishlistId;
    }
}
