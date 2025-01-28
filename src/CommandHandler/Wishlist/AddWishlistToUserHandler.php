<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistToUser;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistHasAnotherShopUserException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddWishlistToUserHandler
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
