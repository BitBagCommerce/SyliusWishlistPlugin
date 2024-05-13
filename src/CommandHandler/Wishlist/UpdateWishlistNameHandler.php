<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;

final class UpdateWishlistNameHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ) {
    }

    public function __invoke(UpdateWishlistName $updateWishlistName): void
    {
        $wishlist = $updateWishlistName->getWishlist();

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        /** @var ?WishlistInterface $existingWishlist */
        $existingWishlist = $this->wishlistRepository->findOneByTokenAndName($wishlistCookieToken, $updateWishlistName->getName());

        if (null !== $existingWishlist) {
            throw new WishlistNameIsTakenException();
        }

        $wishlist->setName($updateWishlistName->getName());
        $this->wishlistRepository->add($wishlist);
    }
}
