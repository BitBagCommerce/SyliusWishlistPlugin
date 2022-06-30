<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class RemoveProductFromWishlist implements RemoveProductFromWishlistInterface
{
    private int $productId;

    private string $wishlistToken;

    public function __construct(int $productId, string $token)
    {
        $this->productId = $productId;
        $this->wishlistToken = $token;
    }

    public function getProductIdValue(): int
    {
        return $this->productId;
    }

    public function getWishlistTokenValue(): string
    {
        return $this->wishlistToken;
    }
}
