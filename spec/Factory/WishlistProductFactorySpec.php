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

namespace spec\Sylius\WishlistPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactory;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;

final class WishlistProductFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductFactory::class);
    }

    public function it_implements_wishlist_product_factory_interface(): void
    {
        $this->shouldHaveType(WishlistProductFactoryInterface::class);
    }

    public function it_creates_wishlist_product(
        FactoryInterface $factory,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $factory->createNew()->willReturn($wishlistProduct);

        $this->createNew()->shouldReturn($wishlistProduct);
    }

    public function it_creates_wishlist_product_for_wishlist_and_product(
        FactoryInterface $factory,
        WishlistProductInterface $wishlistProduct,
        WishlistInterface $wishlist,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        Collection $productVariants,
    ): void {
        $product->getVariants()->willReturn($productVariants);
        $productVariants->first()->willReturn($productVariant);

        $factory->createNew()->willReturn($wishlistProduct);

        $wishlistProduct->setWishlist($wishlist)->shouldBeCalled();
        $wishlistProduct->setProduct($product)->shouldBeCalled();
        $wishlistProduct->setVariant($productVariant)->shouldBeCalled();

        $this->createForWishlistAndProduct($wishlist, $product);
    }

    public function it_creates_wishlist_product_for_wishlist_and_variant(
        FactoryInterface $factory,
        WishlistProductInterface $wishlistProduct,
        WishlistInterface $wishlist,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariant->getProduct()->willReturn($product);

        $factory->createNew()->willReturn($wishlistProduct);

        $wishlistProduct->setWishlist($wishlist)->shouldBeCalled();
        $wishlistProduct->setProduct($product)->shouldBeCalled();
        $wishlistProduct->setVariant($productVariant)->shouldBeCalled();

        $this->createForWishlistAndVariant($wishlist, $productVariant);
    }
}
