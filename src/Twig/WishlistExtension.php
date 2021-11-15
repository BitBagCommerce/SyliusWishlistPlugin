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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sylius\Component\Core\Model\ShopUserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WishlistExtension extends AbstractExtension
{
    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorage $tokenStorage;

    private RequestStack $requestStack;

    public function __construct(

        WishlistRepositoryInterface $wishlistRepository,
        TokenStorage $tokenStorage,
        RequestStack $requestStack
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getWishlists', [$this, 'getWishlists']),
            new TwigFunction('findAllByShopUser', [$this, 'findAllByShopUser']),
            new TwigFunction('findAllByAnonymous', [$this, 'findAllByAnonymous'])
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
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUser($user->getId());
        }
    }

    public function findAllByAnonymous()
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $request->cookies->get('PHPSESSID');
        return $this->wishlistRepository->findAllByAnonymous($cookie);
    }
}