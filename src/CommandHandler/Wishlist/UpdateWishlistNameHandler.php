<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\UpdateWishlistName;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\WishlistNameIsTakenException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;

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
