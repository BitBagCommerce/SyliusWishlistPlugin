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

namespace spec\Sylius\WishlistPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\WishlistPlugin\Checker\ProductQuantityChecker;

final class ProductQuantityCheckerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductQuantityChecker::class);
    }

    public function it_has_positive_number_of_products(
        OrderItemInterface $product,
    ): void {
        $product->getQuantity()->willReturn(4);

        $this->hasPositiveQuantity($product)->shouldReturn(true);
    }

    public function it_has_zero_products(
        OrderItemInterface $product,
    ): void {
        $product->getQuantity()->willReturn(0);

        $this->hasPositiveQuantity($product)->shouldReturn(false);
    }
}
