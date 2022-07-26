<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private OrderItemQuantityModifierInterface $itemQuantityModifier;

    private OrderModifierInterface $orderModifier;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->orderModifier = $orderModifier;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(AddSelectedProductsToCart $addSelectedProductsToCartCommand): void
    {
        $this->addSelectedProductsToCart($addSelectedProductsToCartCommand->getWishlistProducts());
    }

    private function addSelectedProductsToCart(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($this->productCanBeProcessed($wishlistProduct)) {
                $this->addProductToWishlist($wishlistProduct);
            }
        }
    }

    private function productCanBeProcessed(WishlistItem $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        return $this->isInStock($wishlistProduct) && $this->productHasPositiveQuantity($cartItem);
    }

    private function isInStock(WishlistItem $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        if ($wishlistProduct->getCartItem()->getCartItem()->getVariant()->isInStock()) {
            return true;
        }

        $message = sprintf(' "%s" does not have sufficient stock.', $cartItem->getProductName());
        $this->flashBag->add('error', $this->translator->trans($message));

        return false;
    }

    private function productHasPositiveQuantity(OrderItemInterface $product): bool
    {
        if (0 < $product->getQuantity()) {
            return true;
        }
        $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));

        return false;
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

        if (false === $this->flashBag->has('success')){
            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        }

    }
}
