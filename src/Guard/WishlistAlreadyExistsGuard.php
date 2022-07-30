<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Guard;

use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;

class WishlistAlreadyExistsGuard implements WishlistAlreadyExistsGuardInterface
{
    public function check(string $existingWishlistName, string $wishlistToCreate): bool
    {
        if ($existingWishlistName == $wishlistToCreate) {
            return true;
        } else {
            return false;
        }
    }
}
