<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Updater;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;

interface WishlistUpdaterInterface
{
    public function addProductToWishlist(WishlistInterface $wishlist, WishlistProductInterface $product): WishlistInterface;
}
