<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductProcessingCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddSelectedProductsToCartHandler;
use BitBag\SyliusWishlistPlugin\Exception\ProductCantBeAddedToCartException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;

final class AddSelectedProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        ProductProcessingCheckerInterface $productProcessingChecker,
    ): void {
        $this->beConstructedWith(
            $itemQuantityModifier,
            $orderModifier,
            $orderRepository,
            $productProcessingChecker,
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
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        ProductProcessingCheckerInterface $productProcessingChecker,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $productProcessingChecker->canBeProcessed($wishlistProduct)->willReturn(true);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);

        $addToCartCommand->getCart()->willReturn($order);
        $addToCartCommand->getCartItem()->willReturn($orderItem);

        $orderItem->getQuantity()->willReturn(0);
        $itemQuantityModifier->modify($orderItem, 1)->shouldBeCalled();

        $orderModifier->addToOrder($order, $orderItem)->shouldBeCalled();
        $orderRepository->add($order)->shouldBeCalled();

        $addSelectedProductsToCart = new AddSelectedProductsToCart($collection);

        $this->__invoke($addSelectedProductsToCart);
    }

    public function it_doesnt_add_selected_products_to_cart_if_product_cannot_be_processed(
        WishlistItemInterface $wishlistProduct,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        ProductProcessingCheckerInterface $productProcessingChecker,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);

        $productProcessingChecker->canBeProcessed($wishlistProduct)->willReturn(false);

        $wishlistProduct->getCartItem()->shouldNotBeCalled();

        $addToCartCommand->getCart()->shouldNotBeCalled();
        $addToCartCommand->getCartItem()->shouldNotBeCalled();

        $orderItem->getQuantity()->willReturn(0);
        $itemQuantityModifier->modify($orderItem, 1)->shouldNotBeCalled();

        $orderModifier->addToOrder($order, $orderItem)->shouldNotBeCalled();
        $orderRepository->add($order)->shouldNotBeCalled();

        $addSelectedProductsToCart = new AddSelectedProductsToCart($collection);

        $this
            ->shouldThrow(ProductCantBeAddedToCartException::class)
            ->during('__invoke', [$addSelectedProductsToCart])
        ;
    }
}
