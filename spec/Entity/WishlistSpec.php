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

namespace spec\Sylius\WishlistPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Entity\Wishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;

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
