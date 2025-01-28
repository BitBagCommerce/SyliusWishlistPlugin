<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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

final readonly class WishlistContext implements WishlistContextInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistFactoryInterface $wishlistFactory,
        private string $wishlistCookieToken,
        private ChannelContextInterface $channelContext,
        private TokenUserResolverInterface $tokenUserResolver,
    ) {
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
