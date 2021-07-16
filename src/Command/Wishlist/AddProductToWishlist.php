<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

final class AddProductToWishlist implements WishlistTokenValueAwareInterface
{
    public int $productId;

    private WishlistInterface $wishlist;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }

    public function setWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlist = $wishlist;
    }
}
