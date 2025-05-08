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

use Sylius\WishlistPlugin\Command\Wishlist\AddWishlistToUser;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\WishlistHasAnotherShopUserException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddWishlistToUserHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ) {
    }

    public function __invoke(AddWishlistToUser $addWishlistsToUser): void
    {
        $wishlist = $addWishlistsToUser->getWishlist();
        $user = $addWishlistsToUser->getShopUser();
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if ($wishlistCookieToken !== $wishlist->getToken()) {
            throw new WishlistHasAnotherShopUserException();
        }

        if ($this->wishlistRepository->findOneByShopUserAndName($user, (string) $wishlist->getName()) instanceof WishlistInterface) {
            $wishlist->setName($wishlist->getName() . $wishlist->getId());
        }

        $wishlist->setShopUser($user);
        $this->wishlistRepository->add($wishlist);
    }
}
