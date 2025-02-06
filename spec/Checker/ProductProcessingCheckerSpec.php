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
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class ProductProcessingCheckerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductProcessingChecker::class);
    }

    public function it_can_be_processed(
        WishlistItem $wishlistProduct,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
    ): void {
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getQuantity()->willReturn(5);

        $this->canBeProcessed($wishlistProduct)->shouldReturn(true);
    }

    public function it_cannot_be_processed(
        WishlistItem $wishlistProduct,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
    ): void {
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getQuantity()->willReturn(0);

        $this->canBeProcessed($wishlistProduct)->shouldReturn(false);
    }
}
