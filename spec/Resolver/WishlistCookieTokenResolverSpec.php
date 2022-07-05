<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolver;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Request;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

final class WishlistCookieTokenResolverSpec extends ObjectBehavior
{
    public function let(
        RequestStack $requestStack
    ): void
    {
        $this->beConstructedWith(
            $requestStack,
            'token'
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistCookieTokenResolver::class);
        $this->shouldImplement(WishlistCookieTokenResolverInterface::class);
    }

    public function it_resolves_empty_wishlist_cookie_token(
        RequestStack $requestStack,
        ParameterBag $cookies,
        Request $request
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);
        $request->cookies = $cookies;
        $cookies->get('token')->willReturn('');

        $this->resolve()->shouldReturn('');
    }

    public function it_resolves_wishlist_cookie_token(
        RequestStack $requestStack,
        ParameterBag $cookies,
        Request $request
    ): void {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->cookies = $cookies;
        $cookies->get('token')->willReturn('token');

        $this->resolve()->shouldReturn('token');
    }
}
