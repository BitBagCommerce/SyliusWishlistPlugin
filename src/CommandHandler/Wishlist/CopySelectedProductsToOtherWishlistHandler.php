<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductVariantInWishlistCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Creator\WishlistProductVariantCreatorInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantAlreadyInWishlistException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistProductsActionFailedException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class CopySelectedProductsToOtherWishlistHandler
{
    private WishlistRepositoryInterface $wishlistRepository;

    private ProductVariantInWishlistCheckerInterface $productVariantInWishlistChecker;

    private WishlistProductVariantCreatorInterface $wishlistProductVariantCreator;

    private ArrayCollection $unprocessedProductsName;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantInWishlistCheckerInterface $productVariantInWishlistChecker,
        WishlistProductVariantCreatorInterface $wishlistProductVariantCreator
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->productVariantInWishlistChecker = $productVariantInWishlistChecker;
        $this->wishlistProductVariantCreator = $wishlistProductVariantCreator;
        $this->unprocessedProductsName = new ArrayCollection();
    }

    public function __invoke(CopySelectedProductsToOtherWishlist $copySelectedProductsToOtherWishlistCommand): void
    {
        $destinedWishlistId = $copySelectedProductsToOtherWishlistCommand->getDestinedWishlistId();
        $wishlistProducts = $copySelectedProductsToOtherWishlistCommand->getWishlistProducts();

        /** @var WishlistInterface $destinedWishlist */
        $destinedWishlist = $this->wishlistRepository->find($destinedWishlistId);

        $this->copyWishlistProductsToOtherWishlist($wishlistProducts, $destinedWishlist);

        if (0 < count($this->unprocessedProductsName)) {
            $message = 'variant is already in wishlist.';

            throw new WishlistProductsActionFailedException($this->unprocessedProductsName, $message);
        }
    }

    private function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, WishlistInterface $destinedWishlist): void
    {
        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $variant = $wishlistProduct->getCartItem()->getCartItem()->getVariant();

            try {
                $this->productVariantInWishlistChecker->check($destinedWishlist, $variant);
            } catch (ProductVariantAlreadyInWishlistException $exception) {
                $this->unprocessedProductsName->add($wishlistProduct->getWishlistProduct()->getProduct()->getName());
                continue;
            }
            $this->wishlistProductVariantCreator->create($destinedWishlist, $variant);
        }
        $this->wishlistRepository->add($destinedWishlist);
    }
}
