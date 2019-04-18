<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
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
    /** @var FactoryInterface */
    private $wishlistProductFactory;

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
