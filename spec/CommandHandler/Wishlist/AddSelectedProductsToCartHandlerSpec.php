<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddSelectedProductsToCartHandler;
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        RequestStack $requestStack,
        Translator $translator,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $this->beConstructedWith(
            $requestStack,
            $translator,
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
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
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

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->has('success')->willReturn(false);

        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart')->willReturn('Test translation');
        $flashBag->add('success', 'Test translation')->shouldBeCalled();

        $this->__invoke($addSelectedProductsToCart);
    }

    public function it_doesnt_add_selected_products_to_cart_if_product_cannot_be_processed(
        WishlistItemInterface $wishlistProduct,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
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

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

        $translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity')->willReturn('Increase the quantity of at least one item.');
        $flashBag->add('error', 'Increase the quantity of at least one item.')->shouldBeCalled();

        $this->__invoke($addSelectedProductsToCart);
    }
}
