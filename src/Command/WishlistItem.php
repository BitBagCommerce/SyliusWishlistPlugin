<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
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
