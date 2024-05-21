<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class WishlistCookieTokenResolver implements WishlistCookieTokenResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $wishlistCookieToken,
    ) {
    }

    public function resolve(): string
    {
        /** @var ?Request $mainRequest */
        $mainRequest = $this->requestStack->getMainRequest();
        Assert::notNull($mainRequest);

        $wishlistCookieToken = $mainRequest->cookies->get($this->wishlistCookieToken);

        if (null !== $wishlistCookieToken) {
            return (string) $wishlistCookieToken;
        }

        $wishlistCookieToken = $mainRequest->attributes->get($this->wishlistCookieToken);
        if (null !== $wishlistCookieToken) {
            return (string) $wishlistCookieToken;
        }

        return (string) new WishlistToken();
    }
}
