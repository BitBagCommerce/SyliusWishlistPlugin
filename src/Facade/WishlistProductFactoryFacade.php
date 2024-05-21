<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Facade;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class WishlistProductFactoryFacade implements WishlistProductFactoryFacadeInterface
{
    public function __construct(
        private WishlistProductFactoryInterface $wishlistProductFactory,
    ) {
    }

    public function createWithProduct(WishlistInterface $wishlist, ProductInterface $product): void
    {
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);
    }

    public function createWithProductVariant(WishlistInterface $wishlist, ProductVariantInterface $productVariant): void
    {
        $wishlistProductWithVariant = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant);

        $wishlist->addWishlistProduct($wishlistProductWithVariant);
    }
}
