<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Creator;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class WishlistProductVariantCreator implements WishlistProductVariantCreatorInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    public function __construct(WishlistProductFactoryInterface $wishlistProductFactory)
    {
        $this->wishlistProductFactory = $wishlistProductFactory;
    }

    public function create(WishlistInterface $wishlist, ProductVariantInterface $productVariant): void
    {
        $wishlistProductVariant = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant);

        $wishlist->addWishlistProduct($wishlistProductVariant);
    }
}
