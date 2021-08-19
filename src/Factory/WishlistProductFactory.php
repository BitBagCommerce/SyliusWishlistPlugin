<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistProductFactory implements WishlistProductFactoryInterface
{
    private FactoryInterface $wishlistProductFactory;

    public function __construct(FactoryInterface $wishlistProductFactory)
    {
        $this->wishlistProductFactory = $wishlistProductFactory;
    }

    public function createNew(): WishlistProductInterface
    {
        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createNew();

        return $wishlistProduct;
    }

    public function createForWishlistAndProduct(WishlistInterface $wishlist, ProductInterface $product): WishlistProductInterface
    {
        $wishlistProduct = $this->createNew();

        $wishlistProduct->setWishlist($wishlist);
        $wishlistProduct->setProduct($product);
        $wishlistProduct->setVariant($product->getVariants()->first());

        return $wishlistProduct;
    }

    public function createForWishlistAndVariant(WishlistInterface $wishlist, ProductVariantInterface $variant): WishlistProductInterface
    {
        $wishlistProduct = $this->createNew();

        $wishlistProduct->setWishlist($wishlist);
        $wishlistProduct->setProduct($variant->getProduct());
        $wishlistProduct->setVariant($variant);

        return $wishlistProduct;
    }
}
