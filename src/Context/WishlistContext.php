<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Context;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WishlistContext implements WishlistContextInterface
{
    private TokenStorageInterface $tokenStorage;

    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistFactoryInterface $wishlistFactory;

    private string $wishlistCookieToken;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        string $wishlistCookieToken
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function getWishlist(Request $request): WishlistInterface
    {
        $cookieWishlistToken = $request->cookies->get($this->wishlistCookieToken);

        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if (null === $cookieWishlistToken && null === $user) {
            return $this->wishlistFactory->createNew();
        }

        if (null !== $cookieWishlistToken && !$user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findByToken($cookieWishlistToken) ?
                $this->wishlistRepository->findByToken($cookieWishlistToken) :
                $this->wishlistFactory->createNew()
            ;
        }

        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findOneByShopUser($user) ?
                $this->wishlistRepository->findOneByShopUser($user) :
                $this->wishlistFactory->createForUser($user)
            ;
        }

        return $this->wishlistFactory->createNew();
    }
}
