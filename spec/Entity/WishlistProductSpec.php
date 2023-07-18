<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Entity;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
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
