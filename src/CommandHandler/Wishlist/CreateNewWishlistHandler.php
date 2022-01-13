<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateNewWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private RequestStack $requestStack;

    private string $wishlistCookieToken;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        RequestStack $requestStack,
        string $wishlistCookieToken
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->requestStack = $requestStack;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function __invoke(CreateNewWishlist $createNewWishlist): WishlistInterface
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistFactory->createForUser($user);
        } else {
            $wishlist = $this->wishlistFactory->createNew();
        }

        $mainRequest = $this->requestStack->getMasterRequest();

        if ($mainRequest->cookies->get($this->wishlistCookieToken)) {
            $wishlist->setToken($mainRequest->cookies->get($this->wishlistCookieToken));
        }

        $wishlist->setName($createNewWishlist->getName());
        $this->wishlistRepository->add($wishlist);

        return $wishlist;
    }
}