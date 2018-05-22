<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Context;

use BitBag\SyliusWishlistPlugin\Context\WishlistContext;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class WishlistContextSpec extends ObjectBehavior
{
    function let(
        TokenStorageInterface $tokenStorage,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory
    ) {
        $this->beConstructedWith(
            $tokenStorage,
            $wishlistRepository,
            $wishlistFactory,
            'bitbag_sylius_wishlist'
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistContext::class);
    }

    function it_implements_wishlist_context_interface(): void
    {
        $this->shouldHaveType(WishlistContextInterface::class);
    }

    function it_creates_new_wishlist_if_no_cookie_and_user(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist')->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);
        $wishlistFactory->createNew()->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    function it_returns_cookie_wishlist_if_cookie_and_no_user(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist')->willReturn("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId");
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);
        $wishlistRepository->findByToken("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId")->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    function it_returns_new_wishlist_if_cookie_not_found_and_no_user(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist')->willReturn("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId");
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);
        $wishlistRepository->findByToken("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId")->willReturn(null);
        $wishlistFactory->createNew()->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    function it_returns_user_wishlist_if_found_and_user_logged_in(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist')->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);
        $wishlistRepository->findByShopUser($shopUser)->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }

    function it_returns_new_wishlist_if_not_found_and_user_logged_in(
        Request $request,
        ParameterBag $parameterBag,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist
    ): void {
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist')->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);
        $wishlistRepository->findByShopUser($shopUser)->willReturn(null);
        $wishlistFactory->createForUser($shopUser)->willReturn($wishlist);

        $this->getWishlist($request)->shouldReturn($wishlist);
    }
}
