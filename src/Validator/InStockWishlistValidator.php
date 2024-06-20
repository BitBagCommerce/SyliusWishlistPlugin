<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Validator;

use BitBag\SyliusWishlistPlugin\Validator\Constraints\InStockWishlistConstraint;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailability;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class InStockWishlistValidator extends ConstraintValidator
{
    public RequestStack $request;

    public Router $router;

    public AvailabilityCheckerInterface $availabilityChecker;

    public function __construct(
        AvailabilityCheckerInterface $availabilityChecker,
        RequestStack $requestStack,
        Router $router,
    ) {
        $this->request = $requestStack;
        $this->availabilityChecker = $availabilityChecker;
        $this->router = $router;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddToCartCommandInterface::class);

        Assert::isInstanceOf($constraint, CartItemAvailability::class);

        $request = $this->request->getCurrentRequest();

        Assert::notNull($request);

        try {
            $route = $this->router->match($request->getPathInfo());
        } catch (ResourceNotFoundException $exception) {
            throw new AccessDeniedHttpException('Access denied');
        }

        if (array_key_exists('_route', $route) &&
            InStockWishlistConstraint::ADD_PRODUCTS_ROUTE !== $route['_route']) {
            return;
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $value->getCartItem();

        /** @var StockableInterface $variant */
        $variant = $cartItem->getVariant();

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $variant,
            $cartItem->getQuantity() + $this->getExistingCartItemQuantityFromCart($value->getCart(), $cartItem),
        );

        if (!$isStockSufficient) {
            $this->context->addViolation(
                $constraint->message,
                ['%itemName%' => $variant->getInventoryName()],
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): int
    {
        foreach ($cart->getItems() as $existingCartItem) {
            if ($existingCartItem->equals($cartItem)) {
                return $existingCartItem->getQuantity();
            }
        }

        return 0;
    }
}
