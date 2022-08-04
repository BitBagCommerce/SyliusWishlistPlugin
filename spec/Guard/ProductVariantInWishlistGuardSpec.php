<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Guard;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantAlreadyInWishlistException;
use BitBag\SyliusWishlistPlugin\Guard\ProductVariantInWishlistGuard;
use BitBag\SyliusWishlistPlugin\Guard\ProductVariantInWishlistGuardInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantInWishlistGuardSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductVariantInWishlistGuard::class);
        $this->shouldImplement(ProductVariantInWishlistGuardInterface::class);
    }

    public function it_should_throw_exception_if_product_variant_is_already_in_wishlist(
        WishlistInterface $wishlist,
        ProductVariantInterface $productVariant
    ): void {
        $wishlist->hasProductVariant($productVariant)->willReturn(true);

        $this->check($wishlist, $productVariant)
            ->shouldReturn(true);
    }

    public function it_should_return_nothing_if_variant_is_not_in_wishlist(
        WishlistInterface $wishlist,
        ProductVariantInterface $productVariant
    ): void {
        $wishlist->hasProductVariant($productVariant)->willReturn(false);

        $this->check($wishlist, $productVariant)
            ->shouldReturn(false);
    }
}
