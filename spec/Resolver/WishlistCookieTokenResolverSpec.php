<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        RequestStack $requestStack,
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
    ): void {
        $requestStack->getMainRequest()->willReturn($request);
        $request->cookies = new InputBag(['token' => 'wishlist_token']);

        $this->resolve()->shouldReturn('wishlist_token');
    }

    public function it_returns_wishlist_cookie_token_from_main_request_attributes(
        RequestStack $requestStack,
        Request $request,
        ParameterBag $inputBagAttributes,
    ): void {
        $requestStack->getMainRequest()->willReturn($request);
        $inputBagAttributes->get('token')->willReturn('wishlist_token');
        $request->cookies = new InputBag();
        $request->attributes = $inputBagAttributes;

        $this->resolve()->shouldReturn('wishlist_token');
    }

    public function it_returns_new_wishlist_token_class_if_not_found_in_cookies_nor_attributes(
        RequestStack $requestStack,
        Request $request,
        ParameterBag $inputBagAttributes,
        WishlistToken $wishlistToken,
    ): void {
        $requestStack->getMainRequest()->willReturn($request);
        $inputBagAttributes->get('token')->willReturn(null);
        $request->attributes = $inputBagAttributes;
        $request->cookies = new InputBag();
        $wishlistToken->getValue()->willReturn('wishlist_token');

        $this->resolve()->shouldBeString();
    }
}
