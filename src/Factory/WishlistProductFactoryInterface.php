<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Factory;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;

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
