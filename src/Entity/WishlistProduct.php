<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Entity;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class WishlistProduct implements WishlistProductInterface
{
    protected ?int $id;

    protected WishlistInterface $wishlist;

    protected ?ProductInterface $product = null;

    protected ?ProductVariantInterface $variant = null;

    protected int $quantity = 0;

    public function __construct()
    {
        $this->id = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }

    public function setWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlist = $wishlist;
    }

    public function getProduct(): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->product;

        return $product;
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getVariant(): ?ProductVariantInterface
    {
        return $this->variant;
    }

    public function setVariant(?ProductVariantInterface $variant): void
    {
        $this->variant = $variant;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
