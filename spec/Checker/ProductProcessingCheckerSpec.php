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
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\WishlistPlugin\Checker\ProductProcessingChecker;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;

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
