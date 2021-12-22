<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WishlistExtension extends AbstractExtension
{
    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorage $tokenStorage;

    private RequestStack $requestStack;

    private string $wishlistCookieToken;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorage $tokenStorage,
        RequestStack $requestStack,
        string $wishlistCookieToken
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getWishlists', [$this, 'getWishlists']),
            new TwigFunction('findAllByShopUser', [$this, 'findAllByShopUser']),
            new TwigFunction('findAllByAnonymous', [$this, 'findAllByAnonymous']),
        ];
    }

    public function getWishlists()
    {
        /** @var WishlistInterface $wishlists */
        $wishlists = $this->wishlistRepository->findAll();

        return $wishlists;
    }

    public function findAllByShopUser()
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if (!$user instanceof ShopUserInterface) {
            throw new UserNotFoundException();
        }

        return $this->wishlistRepository->findAllByShopUser($user->getId());
    }

    public function findAllByAnonymous()
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $request->cookies->get($this->wishlistCookieToken);

        return $this->wishlistRepository->findAllByAnonymous($cookie);
    }
}
