<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Command\Wishlist;

use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
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
