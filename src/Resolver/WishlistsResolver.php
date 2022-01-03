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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WishlistsResolver implements WishlistsResolverInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorageInterface $tokenStorage;

    private RequestStack $requestStack;

    private string $wishlistCookieToken;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        string $wishlistCookieToken
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function resolve(): ?array
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUser($user->getId());
        }

        $wishlistCookieToken = $this->requestStack->getMainRequest()->cookies->get($this->wishlistCookieToken);

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }
}
