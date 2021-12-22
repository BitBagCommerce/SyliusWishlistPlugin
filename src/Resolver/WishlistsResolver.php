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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WishlistsResolver implements WishlistsResolverInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private string $wishlistCookieToken;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        string $wishlistCookieToken,
        TokenStorageInterface $tokenStorage
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->tokenStorage = $tokenStorage;
    }

    public function resolve(Request $request): ?array
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUser($user->getId());
        }

        return $this->wishlistRepository->findAllByAnonymous($request->cookies->get($this->wishlistCookieToken));
    }
}
