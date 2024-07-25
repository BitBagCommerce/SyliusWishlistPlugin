<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCartInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Exception\InsufficientProductStockException;
use BitBag\SyliusWishlistPlugin\Exception\InvalidProductQuantity;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final class AddProductsToCartHandler
{
    public function __construct(
        private OrderModifierInterface $orderModifier,
        private OrderRepositoryInterface $orderRepository,
        private ?AvailabilityCheckerInterface $availabilityChecker = null,
    ) {
    }

    public function __invoke(AddProductsToCartInterface $addProductsToWishlistCommand): void
    {
        $this->addProductsToWishlist($addProductsToWishlistCommand->getWishlistProducts());
    }

    private function addProductsToWishlist(Collection $wishlistProducts): void
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

    /**
     * @throws InsufficientProductStockException
     */
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

    /**
     * @throws InvalidProductQuantity
     */
    private function productHasPositiveQuantity(OrderItemInterface $product): bool
    {
        if (0 < $product->getQuantity()) {
            return true;
        }

        throw new InvalidProductQuantity();
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

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);
    }
}
