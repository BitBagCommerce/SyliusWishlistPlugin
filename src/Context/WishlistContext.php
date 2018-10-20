<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
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
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var string */
    private $wishlistCookieToken;

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

        /** @var WishlistInterface $newWishlist */
        $newWishlist = $this->wishlistFactory->createNew();
        if (null === $cookieWishlistToken && null === $user) {
            return $newWishlist;
        }

        if (null !== $cookieWishlistToken && !$user instanceof ShopUserInterface) {
            $whishListFromToken = $this->wishlistRepository->findByToken($cookieWishlistToken);
            return $whishListFromToken ?? $newWishlist;
        }

        if ($user instanceof ShopUserInterface) {
            $whishlistForUser = $this->wishlistRepository->findByShopUser($user);
            return  $whishlistForUser ?? $this->wishlistFactory->createForUser($user);
        }

        return $newWishlist;
    }
}
