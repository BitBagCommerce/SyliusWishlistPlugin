<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Updater;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface WishlistUpdaterInterface
{
    public function updateWishlist(WishlistInterface $wishlist): void;

    public function removeWishlist(WishlistInterface $wishlist): void;

    public function addProductToWishlist(WishlistInterface $wishlist, WishlistProductInterface $product): WishlistInterface;

    public function removeProductFromWishlist(WishlistInterface $wishlist, ProductInterface $product): WishlistInterface;

    public function removeProductVariantFromWishlist(WishlistInterface $wishlist, ProductVariantInterface $variant): WishlistInterface;
}
