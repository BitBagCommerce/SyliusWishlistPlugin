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
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WishlistExtension extends AbstractExtension
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

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getWishlists', [$this, 'getWishlists']),
            new TwigFunction('findAllByShopUser', [$this, 'findAllByShopUser']),
            new TwigFunction('findAllByAnonymous', [$this, 'findAllByAnonymous']),
            new TwigFunction('findAllByShopUserAndToken', [$this, 'findAllByShopUserAndToken']),
        ];
    }

    public function getWishlists(): ?array
    {
        /** @var WishlistInterface[] $wishlists */
        $wishlists = $this->wishlistRepository->findAll();

        return $wishlists;
    }

    public function findAllByShopUser(UserInterface $user = null): ?array
    {
        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->wishlistRepository->findAllByShopUser($user->getId());
    }

    public function findAllByShopUserAndToken(UserInterface $user = null): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
    }

    public function findAllByAnonymous(): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }
}
