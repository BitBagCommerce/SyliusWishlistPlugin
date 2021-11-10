<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;

final class AddWishlistProduct implements AddWishlistProductInterface
{
    private WishlistProductInterface $wishlistProduct;

    private AddToCartCommandInterface $cartItem;

    private bool $selected;

    public function getWishlistProduct(): WishlistProductInterface
    {
        return $this->wishlistProduct;
    }

    public function setWishlistProduct(WishlistProductInterface $wishlistProduct): void
    {
        $this->wishlistProduct = $wishlistProduct;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): void
    {
        $this->selected = $selected;
    }

    public function getCartItem(): AddToCartCommandInterface
    {
        return $this->cartItem;
    }

    public function setCartItem(AddToCartCommandInterface $cartItem): void
    {
        $this->cartItem = $cartItem;
    }
}
