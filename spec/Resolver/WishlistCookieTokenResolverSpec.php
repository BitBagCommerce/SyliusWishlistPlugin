<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistToken;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolver;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class WishlistCookieTokenResolverSpec extends ObjectBehavior
{
    public function let(
        RequestStack $requestStack
    ): void {
        $this->beConstructedWith($requestStack, 'token');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistCookieTokenResolver::class);
        $this->shouldImplement(WishlistCookieTokenResolverInterface::class);
    }

    public function it_returns_wishlist_cookie_token_from_main_request_cookies(
        RequestStack $requestStack,
        Request $request,
        ParameterBag $inputBag
    ): void {
        $requestStack->getMainRequest()->willReturn($request);
        $inputBag->get('token')->willReturn('wishlist_token');
        $request->cookies = $inputBag;

        $this->resolve()->shouldReturn('wishlist_token');
    }

    public function it_returns_wishlist_cookie_token_from_main_request_attributes(
        RequestStack $requestStack,
        Request $request,
        ParameterBag $inputBagCookies,
        ParameterBag $inputBagAttributes
    ): void {
        $requestStack->getMainRequest()->willReturn($request);
        $inputBagCookies->get('token')->willReturn(null);
        $inputBagAttributes->get('token')->willReturn('wishlist_token');
        $request->cookies = $inputBagCookies;
        $request->attributes = $inputBagAttributes;

        $this->resolve()->shouldReturn('wishlist_token');
    }

    public function it_returns_new_wishlist_token_class_if_not_found_in_cookies_nor_attributes(
        RequestStack $requestStack,
        Request $request,
        ParameterBag $inputBagCookies,
        ParameterBag $inputBagAttributes,
        WishlistToken $wishlistToken
    ): void {
        $requestStack->getMainRequest()->willReturn($request);
        $inputBagCookies->get('token')->willReturn(null);
        $inputBagAttributes->get('token')->willReturn(null);
        $request->cookies = $inputBagCookies;
        $request->attributes = $inputBagAttributes;
        $wishlistToken->getValue()->willReturn('wishlist_token');

        $this->resolve()->shouldBeString();
    }
}
