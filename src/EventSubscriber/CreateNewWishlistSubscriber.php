<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventSubscriber;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CreateNewWishlistSubscriber implements EventSubscriberInterface
{
    private string $wishlistCookieToken;

    private WishlistsResolverInterface $wishlistsResolver;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private RequestStack $requestStack;

    public function __construct(
        string $wishlistCookieToken,
        WishlistsResolverInterface $wishlistsResolver,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        RequestStack $requestStack,
    ) {
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->wishlistsResolver = $wishlistsResolver;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1]],
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $this->requestStack->getMainRequest();
        $currentPath = $request->getPathInfo();
        if (!str_starts_with($currentPath, '/wishlist')) {
            return;
        }

        /** @var WishlistInterface[] $wishlists */
        $wishlists = $this->wishlistsResolver->resolve();

        $wishlistCookieToken = $request->cookies->get($this->wishlistCookieToken);

        if (!empty($wishlists)) {
            if (null === $wishlistCookieToken) {
                $request->attributes->set($this->wishlistCookieToken, reset($wishlists)->getToken());
            }

            return;
        }

        if (null === $wishlistCookieToken) {
            $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        }

        $request->attributes->set($this->wishlistCookieToken, $wishlistCookieToken);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $this->requestStack->getMainRequest();
        $currentPath = $request->getPathInfo();
        if (!str_starts_with($currentPath, '/wishlist')) {
            return;
        }
        
        if ($request->cookies->has($this->wishlistCookieToken)) {
            return;
        }

        $response = $event->getResponse();
        $wishlistCookieToken = $request->attributes->get($this->wishlistCookieToken);

        if (!$wishlistCookieToken) {
            return;
        }

        $cookie = new Cookie($this->wishlistCookieToken, $wishlistCookieToken, strtotime('+1 year'));
        $response->headers->setCookie($cookie);

        $event->getRequest()->attributes->remove($this->wishlistCookieToken);
    }
}
