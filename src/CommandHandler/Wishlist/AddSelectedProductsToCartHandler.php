<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductProcessingChecker;
use BitBag\SyliusWishlistPlugin\Checker\ProductProcessingCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Exception\ProductCantBeAddedToCartException;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddSelectedProductsToCartHandler implements MessageHandlerInterface
{
    private OrderItemQuantityModifierInterface $itemQuantityModifier;

    private OrderModifierInterface $orderModifier;

    private OrderRepositoryInterface $orderRepository;

    private ProductProcessingCheckerInterface $productProcessingChecker;

    public function __construct(
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        ProductProcessingCheckerInterface $productProcessingChecker
    ) {
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->orderModifier = $orderModifier;
        $this->orderRepository = $orderRepository;
        $this->productProcessingChecker = $productProcessingChecker;
    }

    public function __invoke(AddSelectedProductsToCart $addSelectedProductsToCartCommand): void
    {
        $this->addSelectedProductsToCart($addSelectedProductsToCartCommand->getWishlistProducts());
    }

    private function addSelectedProductsToCart(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($this->productProcessingChecker->productCanBeProcessed($wishlistProduct)) {
                $this->addProductToWishlist($wishlistProduct);
            } else {
                throw new ProductCantBeAddedToCartException();
            }
        }
    }

    private function addProductToWishlist(WishlistItem $wishlistProduct): void
    {
        $cart = $wishlistProduct->getCartItem()->getCart();
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        if (0 === $cartItem->getQuantity()) {
            $this->itemQuantityModifier->modify($cartItem, 1);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);
    }
}
