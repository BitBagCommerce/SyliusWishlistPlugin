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

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistContext implements WishlistContextInterface
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
