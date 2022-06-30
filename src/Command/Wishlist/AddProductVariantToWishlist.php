<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

final class AddProductVariantToWishlist implements AddProductVariantToWishlistInterface
{
    private int $productVariantId;

    private WishlistInterface $wishlist;

    public function __construct(int $productVariantId)
    {
        $this->productVariantId = $productVariantId;
    }

    public function getProductVariantId(): int
    {
        return $this->productVariantId;
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
