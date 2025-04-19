<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
