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

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class WishlistItem implements WishlistItemInterface
{
    private ?WishlistProductInterface $wishlistProduct;

    private ?AddToCartCommandInterface $cartItem;

    private bool $selected;

    public function getWishlistProduct(): ?WishlistProductInterface
    {
        return $this->wishlistProduct;
    }

    public function setWishlistProduct(?WishlistProductInterface $wishlistProduct): void
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

    public function getCartItem(): ?AddToCartCommandInterface
    {
        return $this->cartItem;
    }

    public function setCartItem(?AddToCartCommandInterface $cartItem): void
    {
        $this->cartItem = $cartItem;
    }

    public function getOrderItemQuantity(): int
    {
        /** @var ?AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $this->getCartItem();

        if (null === $addToCartCommand) {
            throw new ResourceNotFoundException();
        }

        return $addToCartCommand->getCartItem()->getQuantity();
    }
}
