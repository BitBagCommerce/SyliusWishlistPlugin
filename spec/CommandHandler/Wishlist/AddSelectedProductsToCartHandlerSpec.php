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
use BitBag\SyliusWishlistPlugin\Exception\InvalidProductQuantityException;
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
