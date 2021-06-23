<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class AddProductToWishlist implements WishlistTokenValueAwareInterface
{
    public int $product;

    private string $token;

    public function __construct(int $product)
    {
        $this->product = $product;
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
