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

namespace Sylius\WishlistPlugin\Resolver;

use Sylius\WishlistPlugin\Entity\WishlistToken;
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
