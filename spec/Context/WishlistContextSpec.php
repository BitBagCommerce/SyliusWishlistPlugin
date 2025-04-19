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

namespace spec\Sylius\WishlistPlugin\Context;

use Sylius\WishlistPlugin\Context\WishlistContext;
use Sylius\WishlistPlugin\Context\WishlistContextInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistContextSpec extends ObjectBehavior
{
    public function let(
        TokenStorageInterface $tokenStorage,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelContextInterface $channelContext,
        TokenUserResolverInterface $tokenUserResolver,
    ) {
        $this->beConstructedWith(
            $tokenStorage,
            $wishlistRepository,
            $wishlistFactory,
            'sylius_wishlist',
            $channelContext,
            $tokenUserResolver,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistContext::class);
    }

    public function it_implements_wishlist_context_interface(): void
    {
        $this->shouldHaveType(WishlistContextInterface::class);
    }

    public function it_creates_new_wishlist_if_no_cookie_and_user(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist,
        TokenUserResolverInterface $tokenUserResolver,
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('sylius_wishlist')->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn(null);

        $wishlistFactory->createNew()->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    public function it_returns_cookie_wishlist_if_cookie_and_no_user(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        TokenUserResolverInterface $tokenUserResolver,
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('sylius_wishlist')->willReturn('Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId');
        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn(null);
        $wishlistRepository->findByToken('Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId')->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    public function it_returns_new_wishlist_if_cookie_not_found_and_no_user(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist,
        TokenUserResolverInterface $tokenUserResolver,
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('sylius_wishlist')->willReturn('Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId');
        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn(null);
        $wishlistRepository->findByToken('Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId')->willReturn(null);
        $wishlistFactory->createNew()->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    public function it_returns_user_wishlist_if_found_and_user_logged_in(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        TokenUserResolverInterface $tokenUserResolver,
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('sylius_wishlist')->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn($shopUser);
        $channelContext->getChannel()->willReturn($channel);
        $wishlistRepository->findOneByShopUserAndChannel($shopUser, $channel)->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    public function it_returns_new_wishlist_if_not_found_and_user_logged_in(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        TokenUserResolverInterface $tokenUserResolver,
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('sylius_wishlist')->willReturn(null);
        $wishlistFactory->createNew()->willReturn($wishlist);
        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn($shopUser);
        $channelContext->getChannel()->willReturn($channel);
        $wishlistRepository->findOneByShopUserAndChannel($shopUser, $channel)->willReturn(null);
        $wishlistFactory->createForUserAndChannel($shopUser, $channel)->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }
}
