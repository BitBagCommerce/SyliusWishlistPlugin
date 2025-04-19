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

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\WishlistPlugin\Command\Wishlist\AddSelectedProductsToCartInterface;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\Exception\InsufficientProductStockException;
use Sylius\WishlistPlugin\Exception\InvalidProductQuantityException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final class AddSelectedProductsToCartHandler
{
    public function __construct(
        private OrderItemQuantityModifierInterface $itemQuantityModifier,
        private OrderModifierInterface $orderModifier,
        private OrderRepositoryInterface $orderRepository,
        private ?AvailabilityCheckerInterface $availabilityChecker = null,
    ) {
    }

    public function __invoke(AddSelectedProductsToCartInterface $addSelectedProductsToCartCommand): void
    {
        $this->addSelectedProductsToCart($addSelectedProductsToCartCommand->getWishlistProducts());
    }

    private function addSelectedProductsToCart(Collection $wishlistProducts): void
    {
        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($this->productCanBeProcessed($wishlistProduct)) {
                $this->addProductToWishlist($wishlistProduct);
            }
        }
    }

    private function productCanBeProcessed(WishlistItemInterface $wishlistProduct): bool
    {
        /** @var ?AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $wishlistProduct->getCartItem();

        if (null === $addToCartCommand) {
            return false;
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addToCartCommand->getCartItem();

        return $this->productIsStockSufficient($cartItem) && $this->productHasPositiveQuantity($cartItem);
    }

    private function productIsStockSufficient(OrderItemInterface $product): bool
    {
        /** @var ?ProductVariantInterface $variant */
        $variant = $product->getVariant();

        if (null === $variant) {
            return false;
        }

        if (null !== $this->availabilityChecker) {
            if ($this->availabilityChecker->isStockSufficient($variant, $product->getQuantity())) {
                return true;
            }
        } elseif ($variant->isInStock()) {
            return true;
        }

        throw new InsufficientProductStockException((string) $product->getProductName());
    }

    private function productHasPositiveQuantity(OrderItemInterface $product): bool
    {
        if (0 < $product->getQuantity()) {
            return true;
        }

        throw new InvalidProductQuantityException();
    }

    private function addProductToWishlist(WishlistItemInterface $wishlistProduct): void
    {
        /** @var ?AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $wishlistProduct->getCartItem();

        if (null === $addToCartCommand) {
            throw new ResourceNotFoundException();
        }

        $cart = $addToCartCommand->getCart();
        $cartItem = $addToCartCommand->getCartItem();

        if (0 === $cartItem->getQuantity()) {
            $this->itemQuantityModifier->modify($cartItem, 1);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);
    }
}
