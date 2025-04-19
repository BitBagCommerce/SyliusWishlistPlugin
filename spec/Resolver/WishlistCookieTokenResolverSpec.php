<?php

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolver;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class WishlistCookieTokenResolverSpec extends ObjectBehavior
{
    public function let(RequestStack $requestStack): void
    {
        $this->beConstructedWith($requestStack, 'token');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistCookieTokenResolver::class);
        $this->shouldImplement(WishlistCookieTokenResolverInterface::class);
    }

    public function it_returns_token_from_cookies_if_present(RequestStack $requestStack): void
    {
        $request = new Request();
        $request->cookies = new InputBag(['token' => 'cookie_token']);
        $requestStack->getMainRequest()->willReturn($request);

        $this->resolve()->shouldReturn('cookie_token');
    }

    public function it_returns_token_from_attributes_if_not_in_cookies(RequestStack $requestStack): void
    {
        $request = new Request();
        $request->cookies = new InputBag();
        $request->attributes = new InputBag(['token' => 'attribute_token']);
        $requestStack->getMainRequest()->willReturn($request);

        $this->resolve()->shouldReturn('attribute_token');
    }

    public function it_returns_new_token_if_not_in_cookies_nor_attributes(RequestStack $requestStack): void
    {
        $request = new Request();
        $request->cookies = new InputBag();
        $request->attributes = new InputBag();
        $requestStack->getMainRequest()->willReturn($request);

        $this->resolve()->shouldMatch("/^([a-f0-9\-]{36})$/");
    }
}
