<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class RemoveProductVariantFromWishlist
{
    private int $productVariantId;

    private string $wishlistToken;

    public function __construct(int $variant, string $wishlistToken)
    {
        $this->productVariantId = $variant;
        $this->wishlistToken = $wishlistToken;
    }

    public function getProductVariantIdValue(): int
    {
        return $this->productVariantId;
    }

    public function getWishlistTokenValue(): string
    {
        return $this->wishlistToken;
    }
}
