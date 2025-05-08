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

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlistInterface;
use Sylius\WishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicatorInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;

final class CopySelectedProductsToOtherWishlistHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistProductsToOtherWishlistDuplicatorInterface $duplicatorProductsToWishlist,
    ) {
    }

    public function __invoke(CopySelectedProductsToOtherWishlistInterface $copySelectedProductsToOtherWishlist): void
    {
        $destinedWishlistId = $copySelectedProductsToOtherWishlist->getDestinedWishlistId();
        $wishlistProducts = $copySelectedProductsToOtherWishlist->getWishlistProducts();

        /** @var WishlistInterface $destinedWishlist */
        $destinedWishlist = $this->wishlistRepository->find($destinedWishlistId);

        $this->duplicatorProductsToWishlist->copyWishlistProductsToOtherWishlist($wishlistProducts, $destinedWishlist);
    }
}
