<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Factory;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistProductFactory implements WishlistProductFactoryInterface
{
    public function __construct(
        private FactoryInterface $wishlistProductFactory,
    ) {
    }

    public function createNew(): WishlistProductInterface
    {
        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createNew();

        return $wishlistProduct;
    }

    public function createForWishlistAndProduct(
        WishlistInterface $wishlist,
        ProductInterface $product,
    ): WishlistProductInterface {
        $wishlistProduct = $this->createNew();

        $wishlistProduct->setWishlist($wishlist);
        $wishlistProduct->setProduct($product);
        /** @var ProductVariantInterface $variant */
        $variant = $product->getVariants()->first();
        $wishlistProduct->setVariant($variant);

        return $wishlistProduct;
    }

    public function createForWishlistAndVariant(
        WishlistInterface $wishlist,
        ProductVariantInterface $variant,
    ): WishlistProductInterface {
        $wishlistProduct = $this->createNew();

        $wishlistProduct->setWishlist($wishlist);
        /** @var ProductInterface $product */
        $product = $variant->getProduct();
        $wishlistProduct->setProduct($product);
        $wishlistProduct->setVariant($variant);

        return $wishlistProduct;
    }
}
