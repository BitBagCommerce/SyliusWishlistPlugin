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
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WishlistExtension extends AbstractExtension
{
    private WishlistRepositoryInterface $wishlistRepository;

    private RequestStack $requestStack;

    private string $wishlistCookieToken;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        RequestStack $requestStack,
        string $wishlistCookieToken
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->requestStack = $requestStack;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getWishlists', [$this, 'getWishlists']),
            new TwigFunction('findAllByShopUser', [$this, 'findAllByShopUser']),
            new TwigFunction('findAllByAnonymous', [$this, 'findAllByAnonymous']),
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
            throw new UserNotFoundException();
        }

        return $this->wishlistRepository->findAllByShopUser($user->getId());
    }

    public function findAllByAnonymous(): ?array
    {
        $request = $this->requestStack->getMainRequest();
        $cookie = $request->cookies->get($this->wishlistCookieToken);

        return $this->wishlistRepository->findAllByAnonymous($cookie);
    }
}
