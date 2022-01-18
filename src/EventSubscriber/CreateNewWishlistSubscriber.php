<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventSubscriber;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateNewWishlistSubscriber implements EventSubscriberInterface
{
    private string $wishlistCookieToken;

    private WishlistsResolverInterface $wishlistsResolver;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        string $wishlistCookieToken,
        WishlistsResolverInterface $wishlistsResolver,
        WishlistFactoryInterface $wishlistFactory,
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->wishlistsResolver = $wishlistsResolver;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
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

        $wishlistCookieToken = $event->getRequest()->cookies->get($this->wishlistCookieToken);

        if ($wishlistCookieToken && !empty($wishlists)) {
            return;
        }

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->createNewWishlist($wishlistCookieToken);

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

    private function createNewWishlist(?string $wishlistCookieToken): WishlistInterface
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        $wishlist = $this->wishlistFactory->createNew();

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistFactory->createForUser($user);
        }

        if ($wishlistCookieToken) {
            $wishlist->setToken($wishlistCookieToken);
        }

        $wishlist->setName('Wishlist');
        $this->wishlistRepository->add($wishlist);

        return $wishlist;
    }

    private function setWishlistCookieToken(Response $response, string $wishlistCookieToken): void
    {
        $cookie = new Cookie($this->wishlistCookieToken, $wishlistCookieToken, strtotime('+1 year'));

        $response->headers->setCookie($cookie);
    }
}
