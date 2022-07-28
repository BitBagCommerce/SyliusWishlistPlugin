<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Checker\ProductCanBeProcessedChecker;
use BitBag\SyliusWishlistPlugin\Checker\ProductQuantityCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductCanBeProcessedCheckerSpec extends ObjectBehavior
{
    public function let(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductQuantityCheckerInterface $productQuantityChecker
    ): void {
        $this->beConstructedWith(
            $flashBag,
            $translator,
            $productQuantityChecker
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductCanBeProcessedChecker::class);
    }

    public function it_can_be_processed(
        WishlistItem $wishlistProduct,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductQuantityCheckerInterface $productQuantityChecker
    ): void {
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getQuantity()->willReturn(5);
        $productQuantityChecker->productHasPositiveQuantity($orderItem)->willReturn(true);

        $this->productCanBeProcessed($wishlistProduct)->shouldReturn(true);
    }

    public function it_can_not_be_processed_due_to_lack_in_stock(
        WishlistItem $wishlistProduct,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductQuantityCheckerInterface $productQuantityChecker,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void {
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getQuantity()->willReturn(0);
        $productQuantityChecker->productHasPositiveQuantity($orderItem)->willReturn(false);
        $orderItem->getProductName()->willReturn('Super yellow shirt');
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.does_not_have_sufficient_stock')->willReturn('does not have sufficient stock.');
        $message = ('Super yellow shirt '.'does not have sufficient stock.');
        $flashBag->add('error', $message)->shouldBeCalled();

        $this->productCanBeProcessed($wishlistProduct)->shouldReturn(false);
    }
}
