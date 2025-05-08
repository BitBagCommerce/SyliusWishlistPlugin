<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistContext implements WishlistContextInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistFactoryInterface $wishlistFactory,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private ChannelContextInterface $channelContext,
        private TokenUserResolverInterface $tokenUserResolver,
    ) {
    }

    public function getWishlist(Request $request): WishlistInterface
    {
        $cookieWishlistToken = $this->wishlistCookieTokenResolver->resolve();

        /** @var ?TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        $user = $this->tokenUserResolver->resolve($token);

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();

        if (!$user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findByToken($cookieWishlistToken) ?? $wishlist;
        }

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            return $wishlist;
        }

        $wishlist = $this->wishlistRepository->findOneByShopUserAndChannel($user, $channel);

        return $wishlist ?? $this->wishlistFactory->createForUserAndChannel($user, $channel);
    }
}
