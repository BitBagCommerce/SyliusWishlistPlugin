<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;

interface WishlistItemInterface
{
    public function getWishlistProduct(): ?WishlistProductInterface;

    public function setWishlistProduct(?WishlistProductInterface $wishlistProduct): void;

    public function isSelected(): ?bool;

    public function setSelected(?bool $selected): void;

    public function getCartItem(): ?AddToCartCommandInterface;

    public function setCartItem(?AddToCartCommandInterface $cartItem): void;
}
