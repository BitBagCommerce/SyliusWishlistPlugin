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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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
        /** @var ?string $cookieWishlistToken */
        $cookieWishlistToken = $request->cookies->get($this->wishlistCookieToken);

        /** @var ?TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();

        $user = null !== $token ? $token->getUser() : null;

        if (null === $cookieWishlistToken && null === $user) {
            return $wishlist;
        }

        if (null !== $cookieWishlistToken && !$user instanceof ShopUserInterface) {
            return null !== $this->wishlistRepository->findByToken($cookieWishlistToken) ?
                $this->wishlistRepository->findByToken($cookieWishlistToken) :
                $wishlist
            ;
        }

        if ($user instanceof ShopUserInterface) {
            return null !== $this->wishlistRepository->findOneByShopUser($user) ?
                $this->wishlistRepository->findOneByShopUser($user) :
                $this->wishlistFactory->createForUser($user)
            ;
        }

        return $wishlist;
    }
}
