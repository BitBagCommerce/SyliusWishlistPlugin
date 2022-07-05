<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Helper;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductToWishlistAdder implements ProductToWishlistAdderInterface
{
    private OrderItemQuantityModifierInterface $itemQuantityModifier;
    private OrderModifierInterface $orderModifier;
    private OrderRepositoryInterface $orderRepository;
    private FlashBagInterface $flashBag;
    private TranslatorInterface $translator;

    public function __construct(
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {

        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->orderModifier = $orderModifier;
        $this->orderRepository = $orderRepository;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function addAndCheckQuantity(WishlistItemInterface $wishlistProduct): void
    {
        $cart = $wishlistProduct->getCartItem()->getCart();
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        if (0 === $cartItem->getQuantity()) {
            $this->itemQuantityModifier->modify($cartItem, 1);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);
    }

    public function addProductToWishlist(WishlistItemInterface $wishlistProduct): void
    {
        $cart = $wishlistProduct->getCartItem()->getCart();
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);

        if (false === $this->flashBag->has('success')){
            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        }
    }
}