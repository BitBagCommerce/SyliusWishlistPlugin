<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactory;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistProductFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductFactory::class);
    }

    function it_implements_wishlist_product_factory_interface(): void
    {
        $this->shouldHaveType(WishlistProductFactoryInterface::class);
    }

    function it_creates_wishlist_product(FactoryInterface $factory, WishlistProductInterface $wishlistProduct): void
    {
        $factory->createNew()->willReturn($wishlistProduct);

        $this->createNew()->shouldReturn($wishlistProduct);
    }

    function it_creates_wishlist_product_for_wishlist_and_product(
        FactoryInterface $factory,
        WishlistProductInterface $wishlistProduct,
        WishlistInterface $wishlist,
        ProductInterface $product
    ): void {
        $factory->createNew()->willReturn($wishlistProduct);

        $wishlistProduct->setWishlist($wishlist)->shouldBeCalled();
        $wishlistProduct->setProduct($product)->shouldBeCalled();

        $this->createForWishlistAndProduct($wishlist, $product);
    }
}
