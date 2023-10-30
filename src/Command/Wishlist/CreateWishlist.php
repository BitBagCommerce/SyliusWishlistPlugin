<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class CreateWishlist implements WishlistSyncCommandInterface
{
    public ?string $tokenValue;

    public ?string $channelCode;

    public function __construct(?string $tokenValue, ?string $channelCode)
    {
        $this->tokenValue = $tokenValue;
        $this->channelCode = $channelCode;
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
