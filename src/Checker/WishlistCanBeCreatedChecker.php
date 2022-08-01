<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Guard\WishlistAlreadyExistsGuardInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class WishlistCanBeCreatedChecker implements WishlistCanBeCreatedCheckerInterface
{
    public WishlistAlreadyExistsGuardInterface $wishlistAlreadyExistsGuard;

    public FlashBagInterface $flashBag;

    public function __construct(
        WishlistAlreadyExistsGuardInterface $wishlistAlreadyExistsGuard,
        FlashBagInterface $flashBag
    ) {
        $this->wishlistAlreadyExistsGuard = $wishlistAlreadyExistsGuard;
        $this->flashBag = $flashBag;
    }

    public function checkIfWishlistNameExists(array $wishlists, string $newWishlistName): void
    {
        /** @var WishlistInterface $wishlist */
        foreach ($wishlists as $wishlist) {
            if ($this->wishlistAlreadyExistsGuard->check($wishlist->getName(), $newWishlistName)) {
                $this->flashBag->add('error', 'message');
                throw new WishlistNameIsTakenException();
            }
        }
    }
}
