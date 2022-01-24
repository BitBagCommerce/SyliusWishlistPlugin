<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Facade;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Facade\WishlistProductFactoryFacade;
use BitBag\SyliusWishlistPlugin\Facade\WishlistProductFactoryFacadeInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class WishlistProductFactoryFacadeSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductFactoryFacade::class);
        $this->shouldImplement(WishlistProductFactoryFacadeInterface::class);
    }

    public function it_should_create_wishlist_product_variant_and_add_it_to_wishlist(
        WishlistInterface $wishlist,
        ProductVariantInterface $productVariant,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct
    ): void {
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)
            ->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)
            ->shouldBeCalled();

        $this->createWithProductVariant($wishlist, $productVariant)
            ->shouldReturn(null);
    }

    public function it_should_create_wishlist_product_and_add_it_to_wishlist(
        WishlistInterface $wishlist,
        ProductInterface $product,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct
    ): void {
        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)
            ->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)
            ->shouldBeCalled();

        $this->createWithProduct($wishlist, $product)
            ->shouldReturn(null);
    }
}
