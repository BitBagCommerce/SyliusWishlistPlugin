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

namespace spec\Sylius\WishlistPlugin\Facade;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Facade\WishlistProductFactoryFacade;
use Sylius\WishlistPlugin\Facade\WishlistProductFactoryFacadeInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class WishlistProductFactoryFacadeSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
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
        WishlistProductInterface $wishlistProduct,
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
        WishlistProductInterface $wishlistProduct,
    ): void {
        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)
            ->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)
            ->shouldBeCalled();

        $this->createWithProduct($wishlist, $product)
            ->shouldReturn(null);
    }
}
