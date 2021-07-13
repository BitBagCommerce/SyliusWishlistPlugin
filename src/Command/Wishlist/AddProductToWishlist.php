<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class AddProductToWishlist implements WishlistTokenValueAwareInterface
{
    public int $productId;

    private string $token;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
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
