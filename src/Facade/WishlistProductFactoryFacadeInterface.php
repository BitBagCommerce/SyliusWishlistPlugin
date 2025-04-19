<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Facade;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @deprecated
 */
interface WishlistProductFactoryFacadeInterface
{
    public function createWithProduct(WishlistInterface $wishlist, ProductInterface $product): void;

    public function createWithProductVariant(WishlistInterface $wishlist, ProductVariantInterface $productVariant): void;
}
