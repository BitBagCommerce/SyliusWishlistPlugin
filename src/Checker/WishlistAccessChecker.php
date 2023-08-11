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
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WishlistAccessChecker implements WishlistAccessCheckerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        TokenStorageInterface $tokenStorage,
        ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->tokenStorage = $tokenStorage;
    }

    public function resolveWishlist(int $wishlistId): ?WishlistInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        return $this->resolveWishlistAccess($wishlist);
    }

    public function resolveWishlistByToken(string $wishlistToken): ?WishlistInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($wishlistToken);

        return $this->resolveWishlistAccess($wishlist);
    }

    private function isWishlistAccessible(
        ?ShopUserInterface $user,
        WishlistInterface $wishlist,
        string $wishlistCookieToken
    ): bool {
        if ($user instanceof ShopUserInterface && $user === $wishlist->getShopUser()) {
            return true;
        }

        if ($wishlistCookieToken === $wishlist->getToken() && null === $wishlist->getShopUser()) {
            return true;
        }

        return false;
    }

    private function resolveWishlistAccess(?WishlistInterface $wishlist): ?WishlistInterface
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (false === $wishlist instanceof WishlistInterface ||
            false === $this->isWishlistAccessible($user, $wishlist, $wishlistCookieToken)) {
            return null;
        }

        return $wishlist;
    }
}
