<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
        ChannelContextInterface $channelContext
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistFactory,
            $channelContext
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
        ShopUserInterface $user
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
        ShopUserInterface $user
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
        ShopUserInterface $user
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
        ShopUserInterface $user
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $wishlistRepository->findOneByShopUser($user)->willReturn($wishlist);
        $wishlistFactory->createForUser($user)->shouldNotBeCalled();

        $this->resolve($user)->shouldReturn($wishlist);
    }
}
