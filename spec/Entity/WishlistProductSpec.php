<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Entity;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;

final class WishlistProductSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProduct::class);
    }

    function it_implements_wishlist_product_interface(): void
    {
        $this->shouldHaveType(WishlistProductInterface::class);
    }

    function it_gets_wishlist(WishlistInterface $wishlist): void
    {
        $this->setWishlist($wishlist);

        $this->getWishlist()->shouldReturn($wishlist);
    }

    function it_gets_product(ProductInterface $product): void
    {
        $this->setProduct($product);

        $this->getProduct()->shouldReturn($product);
    }
}
