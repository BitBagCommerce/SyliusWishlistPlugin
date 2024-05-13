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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

final class CreateNewWishlistSubscriber implements EventSubscriberInterface
{
    private const ALLOWED_ENDPOINTS_PREFIX = '/wishlist';

    public function __construct(
        private string $wishlistCookieToken,
        private WishlistsResolverInterface $wishlistsResolver,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private RequestStack $requestStack,
        private ?Request $mainRequest = null
    ) {
        $this->mainRequest = $this->requestStack->getMainRequest();
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
        Assert::notNull($this->mainRequest, 'The class has to be used in HTTP context only');

        if (!$event->isMainRequest()) {
            return;
        }

        $currentPath = $this->mainRequest->getPathInfo();
        $isWishlistUrl = str_starts_with($currentPath, self::ALLOWED_ENDPOINTS_PREFIX);
        if (!$isWishlistUrl) {
            return;
        }

        /** @var WishlistInterface[] $wishlists */
        $wishlists = $this->wishlistsResolver->resolve();

        $wishlistCookieToken = $this->mainRequest->cookies->get($this->wishlistCookieToken);

        if (!empty($wishlists)) {
            if (null === $wishlistCookieToken) {
                $this->mainRequest->attributes->set($this->wishlistCookieToken, reset($wishlists)->getToken());
            }

            return;
        }

        if (null === $wishlistCookieToken) {
            $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        }

        $this->mainRequest->attributes->set($this->wishlistCookieToken, $wishlistCookieToken);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $tokenWasGenerated = $this->mainRequest->attributes->has($this->wishlistCookieToken);
        $currentPath = $this->mainRequest->getPathInfo();
        $isWishlistUrl = str_starts_with($currentPath, self::ALLOWED_ENDPOINTS_PREFIX);
        if (!$tokenWasGenerated && !$isWishlistUrl) {
            return;
        }

        if ($this->mainRequest->cookies->has($this->wishlistCookieToken)) {
            return;
        }

        $response = $event->getResponse();
        $wishlistCookieToken = $this->mainRequest->attributes->get($this->wishlistCookieToken);

        if (!$wishlistCookieToken) {
            return;
        }

        $cookie = new Cookie($this->wishlistCookieToken, $wishlistCookieToken, strtotime('+1 year'));
        $response->headers->setCookie($cookie);

        $this->mainRequest->attributes->remove($this->wishlistCookieToken);
    }
}
