<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolver;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistsResolverSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $tokenStorage,
            $wishlistCookieTokenResolver,
            $channelContext
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistsResolver::class);
        $this->shouldImplement(WishlistsResolverInterface::class);
    }

    public function it_resolves_wishlist(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $user,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $wishlistCookieTokenResolver->resolve()->willReturn('cookie token');
        $channelContext->getChannel()->willReturn($channel);
        $user->getId()->willReturn(1);

        $wishlistRepository->findAllByShopUserAndToken(1, 'cookie token')->shouldBeCalled();

        $this->resolve();
    }

    public function it_resolves_wishlist_when_user_is_not_found(
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $tokenStorage->getToken()->willReturn(null);
        $wishlistCookieTokenResolver->resolve()->willReturn('cookie token');
        $channelContext->getChannel()->willReturn($channel);

        $wishlistRepository->findAllByAnonymousAndChannel('cookie token', $channel)->shouldBeCalled();

        $this->resolve();
    }

    public function it_resolves_wishlist_when_user_and_channel_not_found(
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $tokenStorage->getToken()->willReturn(null);
        $wishlistCookieTokenResolver->resolve()->willReturn('cookie token');
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $wishlistRepository->findAllByAnonymous('cookie token')->shouldBeCalled();

        $this->resolve();
    }
}
