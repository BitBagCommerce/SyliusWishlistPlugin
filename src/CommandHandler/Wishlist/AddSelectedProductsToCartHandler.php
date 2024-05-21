<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductProcessingCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductCantBeAddedToCartException;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final class AddSelectedProductsToCartHandler
{
    public function __construct(
        private OrderItemQuantityModifierInterface $itemQuantityModifier,
        private OrderModifierInterface $orderModifier,
        private OrderRepositoryInterface $orderRepository,
        private ProductProcessingCheckerInterface $productProcessingChecker,
    ) {
    }

    public function __invoke(AddSelectedProductsToCart $addSelectedProductsToCartCommand): void
    {
        $this->addSelectedProductsToCart($addSelectedProductsToCartCommand->getWishlistProducts());
    }

    private function addSelectedProductsToCart(Collection $wishlistProducts): void
    {
        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($this->productProcessingChecker->canBeProcessed($wishlistProduct)) {
                $this->addProductToWishlist($wishlistProduct);
            } else {
                throw new ProductCantBeAddedToCartException();
            }
        }
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
