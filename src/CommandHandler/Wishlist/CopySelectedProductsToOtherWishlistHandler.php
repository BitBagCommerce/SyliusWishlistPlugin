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
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Services\Copyist\WishlistProductsToOtherWishlistCopyistInterface;

final class CopySelectedProductsToOtherWishlistHandler
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistProductsToOtherWishlistCopyistInterface $copyistProductsToWishlist;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductsToOtherWishlistCopyistInterface $copyistProductsToWishlist
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->copyistProductsToWishlist = $copyistProductsToWishlist;
    }

    public function __invoke(CopySelectedProductsToOtherWishlistInterface $copySelectedProductsToOtherWishlist): void
    {
        $destinedWishlistId = $copySelectedProductsToOtherWishlist->getDestinedWishlistId();
        $wishlistProducts = $copySelectedProductsToOtherWishlist->getWishlistProducts();

        /** @var WishlistInterface $destinedWishlist */
        $destinedWishlist = $this->wishlistRepository->find($destinedWishlistId);

        $this->copyistProductsToWishlist->copyWishlistProductsToOtherWishlist($wishlistProducts, $destinedWishlist);
    }
}
