<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class AddProductVariantToWishlist implements WishlistTokenValueAwareInterface
{
    public int $productVariantId;

    private string $token;

    public function __construct(int $productVariant)
    {
        $this->productVariantId = $productVariant;
    }

    public function getWishlistTokenValue(): string
    {
        return $this->token;
    }

    public function setWishListTokenValue(string $token): void
    {
        $this->token = $token;
    }
}
