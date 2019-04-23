<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Entity;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface WishlistProductInterface extends ResourceInterface
{
    public function getWishlist(): WishlistInterface;

    public function setWishlist(WishlistInterface $wishlist): void;

    public function getProduct(): ProductInterface;

    public function setProduct(ProductInterface $product): void;

    public function getVariant(): ?ProductVariantInterface;

    public function setVariant(?ProductVariantInterface $variant): void;

    public function getQuantity(): int;

    public function setQuantity(int $quantity): void;
}
