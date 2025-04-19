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

use Sylius\WishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\AddSelectedProductsToCartHandler;
use Sylius\WishlistPlugin\Exception\InvalidProductQuantityException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;

final class AddSelectedProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $this->beConstructedWith(
            $itemQuantityModifier,
            $orderModifier,
            $orderRepository,
            $availabilityChecker,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddSelectedProductsToCartHandler::class);
    }

    public function it_adds_selected_products_to_cart(
        WishlistItem $wishlistProduct,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $productVariant,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $addSelectedProductsToCart = new AddSelectedProductsToCart($collection);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCart()->willReturn($order);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(1);
        $addToCartCommand->getCart()->willReturn($order);

        $orderModifier->addToOrder($order, $orderItem)->shouldBeCalled();
        $orderRepository->add($order)->shouldBeCalled();

        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(true);

        $this->shouldNotThrow()->during('__invoke', [$addSelectedProductsToCart]);
    }

    public function it_doesnt_add_selected_products_to_cart_if_product_cannot_be_processed_but_throws_exception(
        WishlistItemInterface $wishlistProduct,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $productVariant,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $addSelectedProductsToCart = new AddSelectedProductsToCart($collection);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(0);
        $addToCartCommand->getCart()->willReturn($order);
        $availabilityChecker->isStockSufficient($productVariant, 0)->willReturn(true);

        $orderModifier->addToOrder($order, $orderItem)->shouldNotBeCalled();
        $orderRepository->add($order)->shouldNotBeCalled();

        $this->shouldThrow(InvalidProductQuantityException::class)->during('__invoke', [$addSelectedProductsToCart]);
    }
}
