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

interface WishlistProductFactoryInterface extends FactoryInterface
{
    public function createForWishlistAndProduct(
        WishlistInterface $wishlist,
        ProductInterface $product,
    ): WishlistProductInterface;

    public function createForWishlistAndVariant(
        WishlistInterface $wishlist,
        ProductVariantInterface $variant,
    ): WishlistProductInterface;
}
