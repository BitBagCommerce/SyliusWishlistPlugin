<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Entity;

use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class WishlistSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Wishlist::class);
    }

    public function it_implements_wishlist_interface(): void
    {
        $this->shouldHaveType(WishlistInterface::class);
    }

    public function it_has_no_products_by_default(): void
    {
        $this->getProducts()->toArray()->shouldReturn([]);
    }

    public function it_has_no_wishlist_products_by_default(): void
    {
        $this->getWishlistProducts()->toArray()->shouldReturn([]);
    }

    public function it_does_not_have_product_by_default(ProductInterface $product): void
    {
        $this->hasProduct($product)->shouldReturn(false);
    }

    public function it_adds_wishlist_product(
        WishlistProductInterface $wishlistProduct,
        ProductVariantInterface $productVariant,
    ): void {
        $wishlistProduct->getVariant()->willReturn($productVariant);

        $wishlistProduct->setWishlist($this)->shouldBeCalled();

        $this->hasWishlistProduct($wishlistProduct)->shouldReturn(false);

        $this->addWishlistProduct($wishlistProduct);

        $this->getWishlistProducts()->contains($wishlistProduct)->shouldReturn(true);
    }

    public function it_gets_shop_user(ShopUserInterface $shopUser): void
    {
        $this->setShopUser($shopUser);

        $this->getShopUser()->shouldReturn($shopUser);
    }
}
