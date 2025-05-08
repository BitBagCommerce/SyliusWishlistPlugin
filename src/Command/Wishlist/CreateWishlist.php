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

namespace Sylius\WishlistPlugin\Command\Wishlist;

final class CreateWishlist implements WishlistSyncCommandInterface
{
    public function __construct(
        public ?string $tokenValue,
        public ?string $channelCode,
    ) {
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function getTokenValue(): ?string
    {
        return $this->tokenValue;
    }
}
