<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class RemoveWishlist
{
    private string $wishlistToken;

    public function __construct(string $wishlistToken)
    {
        $this->wishlistToken = $wishlistToken;
    }

    public function getWishlistTokenValue(): string
    {
        return $this->wishlistToken;
    }
}
