<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistToUser;
use BitBag\SyliusWishlistPlugin\Exception\WishlistHasAnotherShopUserException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddWishlistToUserHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
    }

    public function __invoke(AddWishlistToUser $addWishlistsToUser): void
    {
        $wishlist = $addWishlistsToUser->getWishlist();
        $user = $addWishlistsToUser->getShopUser();
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if ($wishlistCookieToken !== $wishlist->getToken()){
            throw new WishlistHasAnotherShopUserException();
        }

        if ($this->wishlistRepository->findOneByShopUserAndName($user, $wishlist->getName())) {
            $wishlist->setName($wishlist->getName().$wishlist->getId());
        }

        $wishlist->setShopUser($user);
        $this->wishlistRepository->add($wishlist);
    }
}
