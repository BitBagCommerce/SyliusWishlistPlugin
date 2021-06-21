<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

class AddProductToWishlist implements WishlistTokenValueAwareInterface
{
    public int $product;

    protected string $token;

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
