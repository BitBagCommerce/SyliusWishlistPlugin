<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventSubscriber;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateNewWishlistSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1]],
            KernelEvents::RESPONSE => [['onKernelResponse', 0]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var WishlistInterface[] $wishlists */
        $wishlists = $this->wishlistsResolver->resolve();

        if ($event->getRequest()->cookies->has($this->wishlistCookieToken) && !empty($wishlists)) {
            return;
        }

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->createNewWishlist();

        $event->getRequest()->attributes->set($this->wishlistCookieToken, $wishlist->getToken());
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($event->getRequest()->cookies->has($this->wishlistCookieToken)) {
            return;
        }

        $response = $event->getResponse();
        $wishlistCookieToken = $event->getRequest()->attributes->get($this->wishlistCookieToken);

        if (!$wishlistCookieToken) {
            return;
        }
        $this->setWishlistCookieToken($response, $wishlistCookieToken);

        $event->getRequest()->attributes->remove($this->wishlistCookieToken);
    }

    private function createNewWishlist(): WishlistInterface
    {
        $createNewWishlist = new CreateNewWishlist();

        return $this->handle($createNewWishlist);
    }

    private function setWishlistCookieToken(Response $response, string $wishlistCookieToken): void
    {
        $cookie = new Cookie($this->wishlistCookieToken, $wishlistCookieToken, strtotime('+1 year'));

        $response->headers->setCookie($cookie);
    }
}
