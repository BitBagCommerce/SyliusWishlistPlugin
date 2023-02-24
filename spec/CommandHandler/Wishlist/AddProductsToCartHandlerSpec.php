<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCartInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductsToCartHandler;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
    ) {
        $this->beConstructedWith(
            $requestStack,
            $translator,
            $orderModifier,
            $orderRepository,
            $availabilityChecker,
        );
    }
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductsToCartHandler::class);
        $this->shouldHaveType(MessageHandlerInterface::class);
    }

    public function it_do_nothing_if_wishlist_products_empty(
        OrderRepositoryInterface $orderRepository,
    ): void {
        $addProductsToCart = new AddProductsToCart(new ArrayCollection());

        $this->__invoke($addProductsToCart);

        $orderRepository->add(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_do_nothing_if_product_is_not_stock_sufficient(
        AddProductsToCartInterface $addProductsToCart,
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderRepositoryInterface $orderRepository,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $quantity = 4;
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn($quantity);
        $orderItem->getProductName()->shouldBeCalled();

        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $wishlistItem->getCartItem()->willReturn($addToCartCommand);
        $addProductsToCart->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $availabilityChecker->isStockSufficient($productVariant, $quantity)->willReturn(false);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

        $this->__invoke($addProductsToCart);

        $flashBag->add('error', Argument::any())->shouldHaveBeenCalled();
        $orderRepository->add(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_do_nothing_if_quantity_is_not_positive(
        AddProductsToCartInterface $addProductsToCart,
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderRepositoryInterface $orderRepository,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $quantity = -1;
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn($quantity);

        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $wishlistItem->getCartItem()->willReturn($addToCartCommand);
        $addProductsToCart->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $availabilityChecker->isStockSufficient($productVariant, $quantity)->willReturn(true);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

        $this->__invoke($addProductsToCart);

        $flashBag->add('error', Argument::any())->shouldHaveBeenCalled();
        $orderRepository->add(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_is_invokable(
        AddProductsToCartInterface $addProductsToCart,
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $addToCartCommand,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $quantity = 2;
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn($quantity);

        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $addToCartCommand->getCart()->willReturn($order);
        $wishlistItem->getCartItem()->willReturn($addToCartCommand);
        $addProductsToCart->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $availabilityChecker->isStockSufficient($productVariant, $quantity)->willReturn(true);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->has('success')->willReturn(false);

        $this->__invoke($addProductsToCart);

        $flashBag->add('success', Argument::any())->shouldHaveBeenCalled();
        $orderModifier->addToOrder($order, $orderItem)->shouldHaveBeenCalled();
        $orderRepository->add($order)->shouldHaveBeenCalled();
    }
}
