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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductQuantityCheckerSpec extends ObjectBehavior
{
    public function let(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void {
        $this->beConstructedWith(
            $flashBag,
            $translator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductQuantityChecker::class);
    }

    public function it_has_positive_number_of_products(
        OrderItemInterface $product
    ): void {
        $product->getQuantity()->willReturn(4);

        $this->productHasPositiveQuantity($product)->shouldReturn(true);
    }

    public function it_has_zero_products(
        OrderItemInterface $product,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    ): void {
        $product->getQuantity()->willReturn(0);

        $translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity')->willReturn('Increase the quantity of at least one item.');

        $flashBag->add('error', 'Increase the quantity of at least one item.')->shouldBeCalled();
        $this->productHasPositiveQuantity($product)->shouldReturn(false);
    }
}
