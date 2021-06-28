<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class RemoveProductVariantFromWishlist
{
    private int $variant;

    private string $wishlistToken;

    public function __construct(int $variant, string $wishlistToken)
    {
        $this->variant = $variant;
        $this->wishlistToken = $wishlistToken;
    }

    public function getVariantId(): int
    {
        return $this->variant;
    }

    public function getWishlistToken(): string
    {
        return $this->wishlistToken;
    }
}
