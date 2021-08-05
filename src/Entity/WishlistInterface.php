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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface WishlistInterface extends ResourceInterface
{
    /**
     * @return Collection|ProductInterface[]
     */
    public function getProducts(): Collection;

    /**
     * @return Collection|ProductVariantInterface[]
     */
    public function getProductVariants(): Collection;

    public function hasProductVariant(ProductVariantInterface $productVariant): bool;

    /**
     * @return Collection|WishlistProductInterface[]
     */
    public function getWishlistProducts(): Collection;

    public function setWishlistProducts(Collection $wishlistProducts): void;

    public function hasProduct(ProductInterface $product): bool;

    public function hasWishlistProduct(WishlistProductInterface $wishlistProduct): bool;

    public function addWishlistProduct(WishlistProductInterface $wishlistProduct): void;

    public function getShopUser(): ?ShopUserInterface;

    public function setShopUser(ShopUserInterface $shopShopUser): void;

    public function getToken(): string;

    public function setToken(string $token): void;

    public function removeWishlistProduct(WishlistProductInterface $product): self;

    public function removeProductVariant(ProductVariantInterface $variant): self;
}
