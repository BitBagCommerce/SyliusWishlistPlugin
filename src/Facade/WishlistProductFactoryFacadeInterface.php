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
