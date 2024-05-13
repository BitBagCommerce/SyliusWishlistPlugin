<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\CreateWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolver;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;
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
        MessageBusInterface $messageBus
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $tokenStorage,
            $wishlistCookieTokenResolver,
            $channelContext,
            $tokenUserResolver,
            $messageBus
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
        ChannelInterface $channel
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject()
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
        ChannelInterface $channel
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject()
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
        TokenInterface $token
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject()
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
