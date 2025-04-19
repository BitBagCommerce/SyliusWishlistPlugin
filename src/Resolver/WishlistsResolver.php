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

namespace Sylius\WishlistPlugin\Resolver;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Command\Wishlist\CreateWishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WishlistsResolver implements WishlistsResolverInterface
{
    use HandleTrait;

    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private TokenStorageInterface $tokenStorage,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private ChannelContextInterface $channelContext,
        private TokenUserResolverInterface $tokenUserResolver,
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function resolve(): array
    {
        $token = $this->tokenStorage->getToken();
        $user = $this->tokenUserResolver->resolve($token);

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $foundException) {
            $channel = null;
        }

        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
        }

        if ($channel instanceof ChannelInterface) {
            return $this->wishlistRepository->findAllByAnonymousAndChannel($wishlistCookieToken, $channel);
        }

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }

    public function resolveAndCreate(): array
    {
        $wishlists = $this->resolve();

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $foundException) {
            $channel = null;
        }

        if (0 === count($wishlists)) {
            $createWishlist = new CreateWishlist($wishlistCookieToken, $channel?->getCode());
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->handle($createWishlist);

            $wishlists = [$wishlist];
        }

        return $wishlists;
    }
}
