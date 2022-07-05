<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductInStockCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCartInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdderInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductsToCartHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private ProductInStockCheckerInterface $checker;

    private ProductToWishlistAdderInterface $adder;

    public function __construct(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder
    ) {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->checker = $checker;
        $this->adder = $adder;
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
                $this->adder->addProductToWishlist($wishlistProduct);
            }
        }
    }

    private function productCanBeProcessed(WishlistItemInterface $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        return $this->checker->isInStock($wishlistProduct) && $this->productHasPositiveQuantity($cartItem);
    }

    private function productHasPositiveQuantity(OrderItemInterface $product): bool
    {
        if (0 < $product->getQuantity()) {
            return true;
        }
        $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));

        return false;
    }
}
