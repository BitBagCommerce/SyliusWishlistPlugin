<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateNewWishlistListener
{
    use HandleTrait;

    private RequestStack $requestStack;

    private string $wishlistCookieToken;

    private WishlistsResolverInterface $wishlistsResolver;

    public function __construct(
        RequestStack $requestStack,
        string $wishlistCookieToken,
        WishlistsResolverInterface $wishlistsResolver,
        MessageBusInterface $messageBus
    ) {
        $this->requestStack = $requestStack;
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->wishlistsResolver = $wishlistsResolver;
        $this->messageBus = $messageBus;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();

        if (!$request->cookies->has($this->wishlistCookieToken)) {
            return;
        }

        $wishlists = $this->wishlistsResolver->resolve($request);

        if (empty($wishlists)) {
            $response = $event->getResponse();
            $this->createNewWishlist($response);
        }
    }

    private function createNewWishlist(Response $response): void
    {
        $createNewWishlist = new CreateNewWishlist();

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->handle($createNewWishlist);

        $this->setWishlistCookieToken($response, $wishlist);
    }

    private function setWishlistCookieToken(Response $response, WishlistInterface $wishlist): void
    {
        $cookie = new Cookie($this->wishlistCookieToken, $wishlist->getToken(), strtotime('+1 year'));

        $response->headers->setCookie($cookie);
    }
}
