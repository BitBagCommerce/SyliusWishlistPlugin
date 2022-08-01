<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Checker\ProductProcessingChecker;
use BitBag\SyliusWishlistPlugin\Checker\ProductQuantityCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductProcessingCheckerSpec extends ObjectBehavior
{
    public function let(

        ProductQuantityCheckerInterface $productQuantityChecker
    ): void {
        $this->beConstructedWith(
            $productQuantityChecker
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductProcessingChecker::class);
    }

    public function it_can_be_processed(
        WishlistItem $wishlistProduct,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductQuantityCheckerInterface $productQuantityChecker
    ): void {
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $wishlistProduct->getOrderItemQuantity()->willReturn(5);
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
        $wishlistProduct->getOrderItemQuantity()->willReturn(5);
        $productQuantityChecker->productHasPositiveQuantity($orderItem)->willReturn(false);

        $this->productCanBeProcessed($wishlistProduct)->shouldReturn(false);
    }

    public function it_can_not_be_processed_due_to_lack_in_quantity(
        WishlistItem $wishlistProduct,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductQuantityCheckerInterface $productQuantityChecker,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void {
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $wishlistProduct->getOrderItemQuantity()->willReturn(0);
        $productQuantityChecker->productHasPositiveQuantity($orderItem)->willReturn(false);

        $this->productCanBeProcessed($wishlistProduct)->shouldReturn(false);
    }
}
