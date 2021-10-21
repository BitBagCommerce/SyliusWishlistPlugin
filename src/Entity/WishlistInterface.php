<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

    public function removeProduct(WishlistProductInterface $product): self;

    public function removeProductVariant(ProductVariantInterface $variant): self;

    public function getName(): ?string;

    public function setName(?string $name): void;
}
