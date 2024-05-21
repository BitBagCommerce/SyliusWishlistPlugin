<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolver;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserWishlistResolverSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistFactory,
            $channelContext,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopUserWishlistResolver::class);
        $this->shouldImplement(ShopUserWishlistResolverInterface::class);
    }

    public function it_created_new_wishlist_for_shop_user_if_cannot_resolve_with_channel(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        WishlistInterface $wishlist,
        ShopUserInterface $user,
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $wishlistRepository->findOneByShopUserAndChannel($user, $channel)->willReturn(null);
        $wishlistFactory->createForUserAndChannel($user, $channel)->willReturn($wishlist);

        $this->resolve($user)->shouldReturn($wishlist);
    }

    public function it_resolves_wishlist_for_shop_user_with_channel(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        WishlistInterface $wishlist,
        ShopUserInterface $user,
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $wishlistRepository->findOneByShopUserAndChannel($user, $channel)->willReturn($wishlist);
        $wishlistFactory->createForUserAndChannel($user, $channel)->shouldNotBeCalled();

        $this->resolve($user)->shouldReturn($wishlist);
    }

    public function it_created_new_wishlist_for_shop_user_if_cannot_resolve_without_channel(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelContextInterface $channelContext,
        WishlistInterface $wishlist,
        ShopUserInterface $user,
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $wishlistRepository->findOneByShopUser($user)->willReturn(null);
        $wishlistFactory->createForUser($user)->willReturn($wishlist);

        $this->resolve($user)->shouldReturn($wishlist);
    }

    public function it_resolves_wishlist_for_shop_user_without_channel(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelContextInterface $channelContext,
        WishlistInterface $wishlist,
        ShopUserInterface $user,
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $wishlistRepository->findOneByShopUser($user)->willReturn($wishlist);
        $wishlistFactory->createForUser($user)->shouldNotBeCalled();

        $this->resolve($user)->shouldReturn($wishlist);
    }
}
