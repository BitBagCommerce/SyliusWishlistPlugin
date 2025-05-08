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

namespace Sylius\WishlistPlugin\Facade;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;

/**
 * @deprecated
 */
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
