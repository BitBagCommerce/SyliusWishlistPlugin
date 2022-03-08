<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Copyist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Facade\WishlistProductFactoryFacadeInterface;
use BitBag\SyliusWishlistPlugin\Guard\ProductVariantInWishlistGuardInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class WishlistProductsToOtherWishlistCopyist implements WishlistProductsToOtherWishlistCopyistInterface
{
    private ProductVariantInWishlistGuardInterface $productVariantInWishlistChecker;

    private WishlistProductFactoryFacadeInterface $wishlistProductVariantCreator;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        ProductVariantInWishlistGuardInterface $productVariantInWishlistChecker,
        WishlistProductFactoryFacadeInterface $wishlistProductVariantCreator,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository
    ) {
        $this->productVariantInWishlistChecker = $productVariantInWishlistChecker;
        $this->wishlistProductVariantCreator = $wishlistProductVariantCreator;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, WishlistInterface $destinedWishlist): void
    {
        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $variant = $this->productVariantRepository->find($wishlistProduct['variant']);

            $this->productVariantInWishlistChecker->check($destinedWishlist, $variant);
            $this->wishlistProductVariantCreator->createWithProductVariant($destinedWishlist, $variant);
        }
        $this->wishlistRepository->add($destinedWishlist);
    }
}
