<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

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
