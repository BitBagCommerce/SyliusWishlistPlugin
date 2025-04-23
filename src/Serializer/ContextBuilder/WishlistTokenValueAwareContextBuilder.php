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

namespace Sylius\WishlistPlugin\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Serializer\ContextKeys;
use Symfony\Component\HttpFoundation\Request;

final readonly class WishlistTokenValueAwareContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decoratedContextBuilder,
        private WishlistRepositoryInterface $wishlistRepository,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        $token = $request->attributes->get('token');
        if (null === $token) {
            return $context;
        }

        $wishlist = $this->wishlistRepository->findByToken($token);
        if (null !== $wishlist) {
            $context[ContextKeys::WISHLIST] = $wishlist;
        }

        return $context;
    }
}
