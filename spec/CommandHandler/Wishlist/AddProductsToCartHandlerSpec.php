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

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\AddProductsToCartInterface;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\AddProductsToCartHandler;
use Sylius\WishlistPlugin\Exception\InsufficientProductStockException;
use Sylius\WishlistPlugin\Exception\InvalidProductQuantityException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;

final class AddProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $this->beConstructedWith(
            $orderModifier,
            $orderRepository,
            $availabilityChecker,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductsToCartHandler::class);
    }

    public function it_adds_products_from_wishlist_to_cart(
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $productVariant,
        OrderItemInterface $orderItem,
        WishlistItemInterface $wishlistProduct,
        OrderInterface $order,
        AddProductsToCartInterface $addProductsToCart,
        AddToCartCommandInterface $addToCartCommand,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $addProductsToCart->getWishlistProducts()->willReturn($collection);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(1);
        $addToCartCommand->getCart()->willReturn($order);

        $orderModifier->addToOrder($order, $orderItem)->shouldBeCalled();
        $orderRepository->add($order)->shouldBeCalled();

        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(true);

        $this->__invoke($addProductsToCart);
    }

    public function it_throws_exception_when_stock_is_insufficient(
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $productVariant,
        OrderItemInterface $orderItem,
        WishlistItemInterface $wishlistProduct,
        AddProductsToCartInterface $addProductsToCart,
        AddToCartCommandInterface $addToCartCommand,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $addProductsToCart->getWishlistProducts()->willReturn($collection);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(1);

        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(false);
        $orderItem->getProductName()->willReturn('Tested Product');

        $this->shouldThrow(InsufficientProductStockException::class)->during('__invoke', [$addProductsToCart]);
    }

    public function it_throws_exception_when_quantity_is_not_positive(
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $productVariant,
        OrderItemInterface $orderItem,
        WishlistItemInterface $wishlistProduct,
        AddProductsToCartInterface $addProductsToCart,
        AddToCartCommandInterface $addToCartCommand,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $addProductsToCart->getWishlistProducts()->willReturn($collection);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(0);

        $availabilityChecker->isStockSufficient($productVariant, 0)->willReturn(true);

        $this->shouldThrow(InvalidProductQuantityException::class)->during('__invoke', [$addProductsToCart]);
    }
}
