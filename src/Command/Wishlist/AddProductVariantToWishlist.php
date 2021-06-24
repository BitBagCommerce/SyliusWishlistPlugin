<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class AddProductVariantToWishlist implements WishlistTokenValueAwareInterface
{
    public int $productVariant;

    private string $token;

    public function __construct(int $productVariant)
    {
        $this->productVariant = $productVariant;
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
