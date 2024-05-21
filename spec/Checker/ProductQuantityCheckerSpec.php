<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Checker\ProductQuantityChecker;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderItemInterface;

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
