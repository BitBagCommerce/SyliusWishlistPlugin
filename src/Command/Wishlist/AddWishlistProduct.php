<?php
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

class AddWishlistProduct
{
    /** @var WishlistProductInterface  */
    private WishlistProductInterface $wishlistProduct;

    /** @var AddToCartCommandInterface  */
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

