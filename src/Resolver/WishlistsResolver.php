<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Command\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class WishlistsResolver implements WishlistsResolverInterface
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private TokenStorageInterface $tokenStorage,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private ChannelContextInterface $channelContext,
        private TokenUserResolverInterface $tokenUserResolver,
        private MessageBusInterface $messageBus,
    ) {
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
            $wishlist = $this->messageBus->dispatch($createWishlist);

            $wishlists = [$wishlist];
        }

        return $wishlists;
    }
}
