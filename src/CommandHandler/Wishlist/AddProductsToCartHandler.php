<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductsToCartHandler implements MessageHandlerInterface
{
    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    private OrderModifierInterface $orderModifier;

    private OrderRepositoryInterface $orderRepository;

    private ?AvailabilityCheckerInterface $availabilityChecker;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        OrderModifierInterface $orderModifier,
        OrderRepositoryInterface $orderRepository,
        ?AvailabilityCheckerInterface $availabilityChecker = null
    ) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->orderModifier = $orderModifier;
        $this->orderRepository = $orderRepository;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function __invoke(AddProductsToCart $addProductsToWishlistCommand): void
    {
        $this->addProductsToWishlist($addProductsToWishlistCommand->getWishlistProducts());
    }

    private function addProductsToWishlist(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($this->productCanBeProcessed($wishlistProduct)) {
                $this->addProductToWishlist($wishlistProduct);
            }
        }
    }

    private function productCanBeProcessed(WishlistItemInterface $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        return $this->productIsStockSufficient($cartItem) && $this->productHasPositiveQuantity($cartItem);
    }

    private function productIsStockSufficient(OrderItemInterface $product): bool
    {
        if (null !== $this->availabilityChecker) {
            if ($this->availabilityChecker->isStockSufficient($product->getVariant(), $product->getQuantity())) {
                return true;
            }
        } elseif ($product->getVariant()->isInStock()) {
            return true;
        }

        $message = sprintf('%s does not have sufficient stock.', $product->getProductName());

        /** @var Session $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('error', $this->translator->trans($message));

        return false;
    }

    private function productHasPositiveQuantity(OrderItemInterface $product): bool
    {
        if (0 < $product->getQuantity()) {
            return true;
        }
        /** @var Session $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));

        return false;
    }

    private function addProductToWishlist(WishlistItemInterface $wishlistProduct): void
    {
        $cart = $wishlistProduct->getCartItem()->getCart();
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        $this->orderModifier->addToOrder($cart, $cartItem);
        $this->orderRepository->add($cart);

        /** @var Session $session */
        $session = $this->requestStack->getSession();
        $flashBag = $session->getFlashBag();

        if (false === $flashBag->has('success')) {
            $flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        }
    }
}
