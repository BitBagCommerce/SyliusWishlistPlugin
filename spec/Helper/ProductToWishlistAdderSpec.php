<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Helper;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdder;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductToWishlistAdderSpec extends ObjectBehavior
{
    public function let(
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void
    {
        $this->beConstructedWith(
            $itemQuantityModifier,
            $orderModifier,
            $orderRepository,
            $flashBag,
            $translator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductToWishlistAdder::class);
        $this->shouldImplement(ProductToWishlistAdderInterface::class);
    }

    public function it_adds_product_when_quantity_is_positive(
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository
    ): void {
        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCart()->willReturn($cart);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $cartItem->getQuantity()->willReturn(1);

        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();
        $orderRepository->add($cart)->shouldBeCalled();

        $this->addAndCheckQuantity($wishlistItem);
    }

    public function it_adds_product_when_quantity_is_zero(
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        OrderItemQuantityModifierInterface $itemQuantityModifier
    ): void {
        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCart()->willReturn($cart);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $cartItem->getQuantity()->willReturn(0);

        $itemQuantityModifier->modify($cartItem, 1)->shouldBeCalled();
        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();
        $orderRepository->add($cart)->shouldBeCalled();

        $this->addAndCheckQuantity($wishlistItem);
    }

    public function it_adds_product_when_message_is_success(
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        FlashBagInterface $flashBag
    ): void {
        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCart()->willReturn($cart);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $flashBag->has('success')->willReturn(true);

        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();
        $orderRepository->add($cart)->shouldBeCalled();

        $this->addProductToWishlist($wishlistItem);
    }

    public function it_adds_product_when_message_is_not_success(
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        OrderInterface $cart,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void {
        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCart()->willReturn($cart);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $flashBag->has('success')->willReturn(false);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart')->willReturn('translation message');

        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();
        $orderRepository->add($cart)->shouldBeCalled();
        $flashBag->add('success', 'translation message')->shouldBeCalled();

        $this->addProductToWishlist($wishlistItem);
    }
}
