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
