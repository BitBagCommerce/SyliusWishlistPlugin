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

namespace Sylius\WishlistPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface;
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
     * @return Collection<int,ProductVariantInterface|null>
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

    public function clear(): void;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;
}
