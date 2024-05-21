<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
    ) {
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

        $mainRequest = $this->getMainRequest();

        $currentPath = $mainRequest->getPathInfo();
        $isWishlistUrl = str_starts_with($currentPath, self::ALLOWED_ENDPOINTS_PREFIX);
        if (!$isWishlistUrl) {
            return;
        }

        /** @var WishlistInterface[] $wishlists */
        $wishlists = $this->wishlistsResolver->resolve();

        $wishlistCookieToken = $mainRequest->cookies->get($this->wishlistCookieToken);

        if (0 !== count($wishlists)) {
            if (null === $wishlistCookieToken) {
                $mainRequest->attributes->set($this->wishlistCookieToken, reset($wishlists)->getToken());
            }

            return;
        }

        if (null === $wishlistCookieToken) {
            $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        }

        $mainRequest->attributes->set($this->wishlistCookieToken, $wishlistCookieToken);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $mainRequest = $this->getMainRequest();

        $tokenWasGenerated = $mainRequest->attributes->has($this->wishlistCookieToken);
        $currentPath = $mainRequest->getPathInfo();
        $isWishlistUrl = str_starts_with($currentPath, self::ALLOWED_ENDPOINTS_PREFIX);
        if (!$tokenWasGenerated && !$isWishlistUrl) {
            return;
        }

        if ($mainRequest->cookies->has($this->wishlistCookieToken)) {
            return;
        }

        $response = $event->getResponse();
        $wishlistCookieToken = $mainRequest->attributes->get($this->wishlistCookieToken);

        if (null === $wishlistCookieToken || '' === $wishlistCookieToken) {
            return;
        }

        $cookie = new Cookie($this->wishlistCookieToken, $wishlistCookieToken, strtotime('+1 year'));
        $response->headers->setCookie($cookie);

        $mainRequest->attributes->remove($this->wishlistCookieToken);
    }

    private function getMainRequest(): Request
    {
        /** @var ?Request $mainRequest */
        $mainRequest = $this->requestStack->getMainRequest();
        Assert::notNull($mainRequest, 'The class has to be used in HTTP context only');

        return $mainRequest;
    }
}
