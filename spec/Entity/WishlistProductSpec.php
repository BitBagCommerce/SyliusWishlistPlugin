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

namespace spec\Sylius\WishlistPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProduct;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;

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
