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
use Symfony\Component\HttpFoundation\RequestStack;

final class WishlistCookieTokenResolver implements WishlistCookieTokenResolverInterface
{
    private RequestStack $requestStack;

    private string $wishlistCookieToken;

    private ?string $generatedToken = null;

    public function __construct(
        RequestStack $requestStack,
        string $wishlistCookieToken
    ) {
        $this->requestStack = $requestStack;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function resolve(): string
    {
        if (null !== $this->generatedToken) {
            return $this->generatedToken;
        }

        $wishlistCookieToken = $this->requestStack->getMainRequest()->cookies->get($this->wishlistCookieToken);

        if (!$wishlistCookieToken) {
            $newToken = (string) new WishlistToken();
            $this->generatedToken = $newToken;

            return $newToken;
        }

        $this->generatedToken = $wishlistCookieToken;

        return $wishlistCookieToken;
    }
}
