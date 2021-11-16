<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig\Environment;

final class ListWishlistsAction
{
    private WishlistRepositoryInterface $wishlistRepository;

    private Environment $twigEnvironment;

    private TokenStorage $tokenStorage;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        Environment $twigEnvironment,
        TokenStorage $tokenStorage
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->twigEnvironment = $twigEnvironment;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        $cookie = $request->cookies->get('PHPSESSID');

        if ($user instanceof ShopUserInterface) {
            $wishlists = $this->wishlistRepository->findAllByShopUser($user->getId());
        } else {
            if ($cookie == null) {
                $session = new Session();
                $session->start();
                $cookie = $session->getId();
                $wishlists = $this->wishlistRepository->findAllByAnonymous($cookie);
            } else {
                $wishlists = $this->wishlistRepository->findAllByAnonymous($cookie);
            }
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistGroup/index.html.twig', [
                'wishlist' => $wishlists,
            ])
        );
    }
}
