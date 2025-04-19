<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Resolver;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\WishlistPlugin\Resolver\WishlistsResolver;
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistsResolverSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        TokenUserResolverInterface $tokenUserResolver,
        MessageBusInterface $messageBus,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $tokenStorage,
            $wishlistCookieTokenResolver,
            $channelContext,
            $tokenUserResolver,
            $messageBus,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistsResolver::class);
        $this->shouldImplement(WishlistsResolverInterface::class);
    }

    public function it_resolves_wishlists_by_shop_user_and_token(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        TokenUserResolverInterface $tokenUserResolver,
        WishlistInterface $wishlist,
        TokenInterface $token,
        ShopUserInterface $user,
        ChannelInterface $channel,
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject(),
        ];

        $wishlistToken = 'wishlist_token';

        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn($user);
        $wishlistCookieTokenResolver->resolve()->willReturn($wishlistToken);
        $channelContext->getChannel()->willReturn($channel);
        $user->getId()->willReturn(1);
        $wishlistRepository->findAllByShopUserAndToken(1, $wishlistToken)->willReturn($wishlists);

        $this->resolve()->shouldReturn($wishlists);
    }

    public function it_resolves_wishlists_by_token_and_channel_without_user(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        TokenUserResolverInterface $tokenUserResolver,
        WishlistInterface $wishlist,
        TokenInterface $token,
        ChannelInterface $channel,
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject(),
        ];

        $wishlistToken = 'wishlist_token';

        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn(null);
        $wishlistCookieTokenResolver->resolve()->willReturn($wishlistToken);
        $channelContext->getChannel()->willReturn($channel);
        $wishlistRepository->findAllByAnonymousAndChannel($wishlistToken, $channel)->willReturn($wishlists);

        $this->resolve()->shouldReturn($wishlists);
    }

    public function it_resolves_wishlists_by_token(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        TokenUserResolverInterface $tokenUserResolver,
        WishlistInterface $wishlist,
        TokenInterface $token,
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject(),
        ];

        $wishlistToken = 'wishlist_token';

        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn(null);
        $wishlistCookieTokenResolver->resolve()->willReturn($wishlistToken);
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $wishlistRepository->findAllByAnonymous($wishlistToken)->willReturn($wishlists);

        $this->resolve()->shouldReturn($wishlists);
    }
}
