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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

class Wishlist implements WishlistInterface
{
    protected ?int $id = null;

    /** @var Collection|WishlistProductInterface[] */
    protected $wishlistProducts;

    protected ?ShopUserInterface $shopUser = null;

    /** @var WishlistTokenInterface|null */
    protected $token;

    public function __construct()
    {
        $this->wishlistProducts = new ArrayCollection();
        $this->token = new WishlistToken();
        $this->id = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducts(): Collection
    {
        $products = [];

        foreach ($this->wishlistProducts as $wishlistProduct) {
            $products[] = $wishlistProduct->getProduct();
        }

        return new ArrayCollection($products);
    }

    /**
     * @return Collection<int,ProductVariantInterface|null>
     */
    public function getProductVariants(): Collection
    {
        $variants = [];

        foreach ($this->wishlistProducts as $wishlistProduct) {
            $variants[] = $wishlistProduct->getVariant();
        }

        return new ArrayCollection($variants);
    }

    public function hasProductVariant(ProductVariantInterface $productVariant): bool
    {
        foreach ($this->wishlistProducts as $wishlistProduct) {
            if ($productVariant === $wishlistProduct->getVariant()) {
                return true;
            }
        }

        return false;
    }

    public function getWishlistProducts(): Collection
    {
        return $this->wishlistProducts;
    }

    public function hasProduct(ProductInterface $product): bool
    {
        foreach ($this->wishlistProducts as $wishlistProduct) {
            if ($product === $wishlistProduct->getProduct()) {
                return true;
            }
        }

        return false;
    }

    public function setWishlistProducts(Collection $wishlistProducts): void
    {
        $this->wishlistProducts = $wishlistProducts;
    }

    public function hasWishlistProduct(WishlistProductInterface $wishlistProduct): bool
    {
        return $this->wishlistProducts->contains($wishlistProduct);
    }

    public function addWishlistProduct(WishlistProductInterface $wishlistProduct): void
    {
        if (!$this->hasProductVariant($wishlistProduct->getVariant())) {
            $wishlistProduct->setWishlist($this);
            $this->wishlistProducts->add($wishlistProduct);
        }
    }

    public function getShopUser(): ?ShopUserInterface
    {
        return $this->shopUser;
    }

    public function setShopUser(ShopUserInterface $shopUser): void
    {
        $this->shopUser = $shopUser;
    }

    public function getToken(): string
    {
        return (string) $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = new WishlistToken($token);
    }

    public function removeWishlistProduct(WishlistProductInterface $product): self
    {
        if ($this->hasWishlistProduct($product)) {
            $this->wishlistProducts->removeElement($product);
        }

        return $this;
    }

    public function removeProduct(ProductInterface $product): self
    {
        foreach ($this->wishlistProducts as $wishlistProduct) {
            if ($wishlistProduct->getProduct() === $product) {
                $this->wishlistProducts->removeElement($wishlistProduct);
            }
        }

        return $this;
    }

    public function removeProductVariant(ProductVariantInterface $variant): self
    {
        foreach ($this->wishlistProducts as $wishlistProduct) {
            if ($wishlistProduct->getVariant() === $variant) {
                $this->wishlistProducts->removeElement($wishlistProduct);
            }
        }

        return $this;
    }
}
