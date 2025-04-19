<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Duplicator;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Doctrine\Common\Collections\Collection;

interface WishlistProductsToOtherWishlistDuplicatorInterface
{
    public function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, WishlistInterface $destinedWishlist): void;
}
