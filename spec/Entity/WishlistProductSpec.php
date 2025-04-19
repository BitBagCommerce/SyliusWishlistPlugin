<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Entity;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProduct;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;

final class WishlistProductSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProduct::class);
    }

    public function it_implements_wishlist_product_interface(): void
    {
        $this->shouldHaveType(WishlistProductInterface::class);
    }

    public function it_gets_wishlist(WishlistInterface $wishlist): void
    {
        $this->setWishlist($wishlist);

        $this->getWishlist()->shouldReturn($wishlist);
    }

    public function it_gets_product(ProductInterface $product): void
    {
        $this->setProduct($product);

        $this->getProduct()->shouldReturn($product);
    }
}
