<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WishlistsResolver implements WishlistsResolverInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorageInterface $tokenStorage;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
    }

    public function resolve(): array
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
        }

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }
}
