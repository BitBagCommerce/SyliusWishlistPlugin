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

final class CreateNewWishlist implements WishlistSyncCommandInterface
{
    public function __construct(
        public string $name,
        public ?string $channelCode = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }
}
