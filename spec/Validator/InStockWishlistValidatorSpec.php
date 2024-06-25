<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Validator;

use BitBag\SyliusWishlistPlugin\Validator\InStockWishlistValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailability;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class InStockWishlistValidatorSpec extends ObjectBehavior
{
    public function let(
        AvailabilityCheckerInterface $availabilityChecker,
        RequestStack $requestStack,
        Router $router,
    ): void {
        $this->beConstructedWith($availabilityChecker, $requestStack, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(InStockWishlistValidator::class);
    }

    public function it_should_throw_exception_if_value_is_not_instance_of_command_interface(
        Constraint $constraint,
        stdClass $value,
    ): void {
        $this->shouldThrow(\Exception::class)->during(
            'validate',
            [$value, $constraint],
        );
    }

    public function it_should_throw_exception_if_constraint_is_not_instance_of_command_interface(
        Constraint $constraint,
        AddToCartCommandInterface $value,
    ): void {
        $this->shouldThrow(\Exception::class)->during(
            'validate',
            [$value, $constraint],
        );
    }

    public function it_should_throw_exception_if_request_is_null(
        RequestStack $requestStack,
        AddToCartCommandInterface $value,
    ): void {
        $constraint = new CartItemAvailability();

        $requestStack->getCurrentRequest()->willReturn(null);

        $this->shouldThrow(\Exception::class)->during(
            'validate',
            [$value, $constraint],
        );
    }

    public function it_should_do_nothing_if_the_request_is_not_add_to_cart(
        RequestStack $requestStack,
        AddToCartCommandInterface $value,
        Request $request,
        Router $router,
    ): void {
        $constraint = new CartItemAvailability();
        $exampleArray = [
            '_route' => 'bitbag_sylius_wishlist_plugin_shop_locale_wishlist_remove_selected_products',
            '_controller' => 'bitbag_sylius_wishlist_plugin.controller.action.remove_selected_products_from_wishlist',
            '_locale' => 'en_US',
            'wishlistId' => '4',
        ];

        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getPathInfo()->willReturn('/en_US/wishlist/4/products/delete');
        $router->match('/en_US/wishlist/4/products/delete')->willReturn($exampleArray);

        $this->validate($value, $constraint)->shouldReturn(null);
    }

    public function it_should_do_nothing_if_current_stock_is_not_empty(
        AvailabilityCheckerInterface $availabilityChecker,
        RequestStack $requestStack,
        AddToCartCommandInterface $value,
        Request $request,
        Router $router,
        OrderItemInterface $cartItem,
        ExecutionContextInterface $context,
        ProductVariantInterface $variant,
        OrderInterface $order,
    ): void {
        $constraint = new CartItemAvailability();
        $exampleArray = ['_route' => 'bitbag_sylius_wishlist_plugin_shop_locale_wishlist_add_selected_products',
            '_controller' => 'bitbag_sylius_wishlist_plugin.controller.action.add_selected_products_to_cart',
            '_locale' => 'en_US',
            'wishlistId' => '4',
        ];

        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getPathInfo()->willReturn('/en_US/wishlist/4/products/add');
        $router->match('/en_US/wishlist/4/products/add')->willReturn($exampleArray);
        $value->getCartItem()->willReturn($cartItem);
        $cartItem->getVariant()->willReturn($variant);
        $cartItem->getQuantity()->willReturn(5);
        $value->getCart()->willReturn($order);
        $itemsCollection = new ArrayCollection([$cartItem->getWrappedObject()]);
        $order->getItems()->willReturn($itemsCollection);
        $cartItem->equals(Argument::any())->willReturn(false);
        $availabilityChecker->isStockSufficient($variant, 5)->willReturn(true);

        $context->addViolation(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint)->shouldReturn(null);
    }

    public function it_should_do_build_violation_if_current_stock_is_empty_and_user_added_to_cart_item_from_wishlist(
        AvailabilityCheckerInterface $availabilityChecker,
        RequestStack $requestStack,
        AddToCartCommandInterface $value,
        Request $request,
        Router $router,
        OrderItemInterface $cartItem,
        OrderInterface $order,
        ProductVariantInterface $variant,
        ExecutionContextInterface $context,
    ): void {
        $constraint = new CartItemAvailability();
        $constraint->message = 'sylius.cart_item.not_available';
        $exampleArray = ['_route' => 'bitbag_sylius_wishlist_plugin_shop_locale_wishlist_add_selected_products',
            '_controller' => 'bitbag_sylius_wishlist_plugin.controller.action.add_selected_products_to_cart',
            '_locale' => 'en_US',
            'wishlistId' => '4',
        ];

        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getPathInfo()->willReturn('/en_US/wishlist/4/products/add');
        $router->match('/en_US/wishlist/4/products/add')->willReturn($exampleArray);
        $value->getCartItem()->willReturn($cartItem);
        $cartItem->getVariant()->willReturn($variant);
        $cartItem->getQuantity()->willReturn(5);
        $value->getCart()->willReturn($order);
        $itemsCollection = new ArrayCollection([$cartItem->getWrappedObject()]);
        $order->getItems()->willReturn($itemsCollection);
        $cartItem->equals(Argument::any())->willReturn(false);
        $availabilityChecker->isStockSufficient($variant, 5)->willReturn(false);
        $variant->getInventoryName()->willReturn('Red T-shirt');

        $context->addViolation(
            $constraint->message,
            ['%itemName%' => 'Red T-shirt'],
        )->shouldBeCalled();

        $this->initialize($context);
        $this->validate($value, $constraint);
    }
}
