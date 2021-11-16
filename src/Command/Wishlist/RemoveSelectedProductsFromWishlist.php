<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

final class RemoveSelectedProductsFromWishlist
{
    /** @var object|AddWishlistProduct[] */
    private $wishlistProducts;

    private WishlistInterface $wishlist;

    public function __construct(object $wishlistProducts, WishlistInterface $wishlist)
    {
        $this->wishlistProducts = $wishlistProducts;
        $this->wishlist = $wishlist;
    }

    public function getWishlistProducts(): ?object
    {
        return $this->wishlistProducts;
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }
}
