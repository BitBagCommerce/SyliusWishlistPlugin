<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

final class AddProductVariantToWishlist implements WishlistTokenValueAwareInterface
{
    public int $productVariantId;

    private WishlistInterface $wishlist;

    public function __construct(int $productVariantId)
    {
        $this->productVariantId = $productVariantId;
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
