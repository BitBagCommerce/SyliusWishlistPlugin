<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Entity;

use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class WishlistSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(Wishlist::class);
    }

    function it_implements_wishlist_interface(): void
    {
        $this->shouldHaveType(WishlistInterface::class);
    }

    function it_has_null_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_products_by_default(): void
    {
        $this->getProducts()->toArray()->shouldReturn([]);
    }

    function it_has_no_wishlist_products_by_default(): void
    {
        $this->getWishlistProducts()->toArray()->shouldReturn([]);
    }

    function it_does_not_have_product_by_default(ProductInterface $product): void
    {
        $this->hasProduct($product)->shouldReturn(false);
    }

    function it_adds_wishlist_product(WishlistProductInterface $wishlistProduct, ProductInterface $product): void
    {
        $wishlistProduct->getProduct()->willReturn($product);

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
