<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;

interface WishlistItemInterface extends WishlistSyncCommandInterface
{
    public function getWishlistProduct(): ?WishlistProductInterface;

    public function setWishlistProduct(?WishlistProductInterface $wishlistProduct): void;

    public function isSelected(): bool;

    public function setSelected(bool $selected): void;

    public function getCartItem(): ?AddToCartCommandInterface;

    public function setCartItem(?AddToCartCommandInterface $cartItem): void;

    public function getOrderItemQuantity(): int;
}
