<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
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

    public function __invoke(AddSelectedProductsToCart $addSelectedProductsToCart): void
    {
        /** @var AddWishlistProduct $wishlistProduct */
        foreach ($addSelectedProductsToCart->getWishlistProducts() as $wishlistProduct) {
            if (!$wishlistProduct->isSelected()) {
                continue;
            }

            /** @var AddToCartCommandInterface $addToCartCommand */
            $addToCartCommand = $wishlistProduct->getCartItem();
            $cart = $addToCartCommand->getCart();
            $cartItem = $addToCartCommand->getCartItem();

            if (!$this->isInStock($cartItem)) {
                continue;
            }

            $this->addProductToWishlist($cartItem, $cart);
        }
    }

    private function isInStock(OrderItemInterface $cartItem): bool
    {
        if (!$cartItem->getVariant()->isInStock()) {
            $message = sprintf('%s does not have sufficient stock.', $cartItem->getProductName());
            $this->flashBag->add('error', $this->translator->trans($message));

            return false;
        }

        return true;
    }

    private function addProductToWishlist(OrderItemInterface $cartItem, OrderInterface $cart): void
    {
        if (0 === $cartItem->getQuantity()) {
            $this->itemQuantityModifier->modify($cartItem, 1);
        }

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);

        $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_selected_wishlist_items_to_cart'));
    }
}
