<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Creator;

use BitBag\SyliusWishlistPlugin\Creator\WishlistProductVariantCreator;
use BitBag\SyliusWishlistPlugin\Creator\WishlistProductVariantCreatorInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class WishlistProductVariantCreatorSpec extends ObjectBehavior
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
        $this->shouldHaveType(WishlistProductVariantCreator::class);
        $this->shouldImplement(WishlistProductVariantCreatorInterface::class);
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

        $this->create($wishlist, $productVariant)
            ->shouldReturn(null);
    }
}
