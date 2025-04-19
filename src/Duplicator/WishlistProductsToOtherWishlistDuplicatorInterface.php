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

namespace Sylius\WishlistPlugin\Duplicator;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Doctrine\Common\Collections\Collection;

interface WishlistProductsToOtherWishlistDuplicatorInterface
{
    public function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, WishlistInterface $destinedWishlist): void;
}
