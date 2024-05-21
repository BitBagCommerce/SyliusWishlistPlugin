<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCartInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductsToCartHandler;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
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
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
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

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->has('success')->willReturn(false);

        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart')->willReturn('Test translation');
        $flashBag->add('success', 'Test translation')->shouldBeCalled();

        $this->__invoke($addProductsToCart);
    }

    public function it_doesnt_add_products_from_wishlist_to_cart_if_stock_is_insufficient(
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $productVariant,
        OrderItemInterface $orderItem,
        WishlistItemInterface $wishlistProduct,
        OrderInterface $order,
        AddProductsToCartInterface $addProductsToCart,
        AddToCartCommandInterface $addToCartCommand,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
    ): void {
        $collection = new ArrayCollection([$wishlistProduct->getWrappedObject()]);
        $addProductsToCart->getWishlistProducts()->willReturn($collection);

        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(0);
        $addToCartCommand->getCart()->willReturn($order);
        $availabilityChecker->isStockSufficient($productVariant, 0)->willReturn(false);

        $orderItem->getProductName()->willReturn('Tested Product');

        $orderModifier->addToOrder($order, $orderItem)->shouldNotBeCalled();
        $orderRepository->add($order)->shouldNotBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $translator->trans('Tested Product does not have sufficient stock.')->willReturn('Translation test');
        $flashBag->add('error', 'Translation test')->shouldBeCalled();

        $this->__invoke($addProductsToCart);
    }
}
