<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Context;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistContext implements WishlistContextInterface
{
    private TokenStorageInterface $tokenStorage;

    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistFactoryInterface $wishlistFactory;

    private string $wishlistCookieToken;

    private ChannelContextInterface $channelContext;

    private TokenUserResolverInterface $tokenUserResolver;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        string $wishlistCookieToken,
        ChannelContextInterface $channelContext,
        TokenUserResolverInterface $tokenUserResolver,
        ) {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->channelContext = $channelContext;
        $this->tokenUserResolver = $tokenUserResolver;
    }

    public function getWishlist(Request $request): WishlistInterface
    {
        /** @var ?string $cookieWishlistToken */
        $cookieWishlistToken = $request->cookies->get($this->wishlistCookieToken);

        /** @var ?TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();

        $user = $this->tokenUserResolver->resolve($token);

        if (null === $cookieWishlistToken && null === $user) {
            return $wishlist;
        }

        if (null !== $cookieWishlistToken && !$user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findByToken($cookieWishlistToken) ?? $wishlist;
        }

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $channel = null;
        }

        if (null !== $channel) {
            if ($user instanceof ShopUserInterface) {
                $wishlist = $this->wishlistRepository->findOneByShopUserAndChannel($user, $channel);

                return $wishlist ?? $this->wishlistFactory->createForUserAndChannel($user, $channel);
            }
        }

        return $wishlist;
    }
}
