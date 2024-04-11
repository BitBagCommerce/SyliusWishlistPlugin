<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface AddProductToSelectedWishlistInterface
{
    public function getWishlist(): WishlistInterface;

    public function getProduct(): ProductInterface;
}
