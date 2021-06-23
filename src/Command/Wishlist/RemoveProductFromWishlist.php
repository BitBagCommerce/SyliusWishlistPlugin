<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class RemoveProductFromWishlist
{
    private int $productId;

    private string $wishlistToken;

    public function __construct(int $product, string $wishlistToken)
    {
        $this->productId = $product;
        $this->wishlistToken = $wishlistToken;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getWishlistToken(): string
    {
        return $this->wishlistToken;
    }
}
