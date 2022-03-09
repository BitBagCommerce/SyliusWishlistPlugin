<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlistInterface;
use BitBag\SyliusWishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicatorInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

final class CopySelectedProductsToOtherWishlistHandler
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistProductsToOtherWishlistDuplicatorInterface $duplicatorProductsToWishlist;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductsToOtherWishlistDuplicatorInterface $duplicatorProductsToWishlist
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->duplicatorProductsToWishlist = $duplicatorProductsToWishlist;
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
