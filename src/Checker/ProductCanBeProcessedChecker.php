<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductCanBeProcessedChecker implements ProductCanBeProcessedCheckerInterface
{
    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private ProductQuantityCheckerInterface $productQuantityChecker;

    public function __construct(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductQuantityCheckerInterface $productQuantityChecker
    ) {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->productQuantityChecker = $productQuantityChecker;
    }

    public function productCanBeProcessed(WishlistItem $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        return $this->isInStock($wishlistProduct) && $this->productQuantityChecker->productHasPositiveQuantity($cartItem);
    }

    private function isInStock(WishlistItem $wishlistProduct): bool
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();

        if (0 < $wishlistProduct->getCartItem()->getCartItem()->getQuantity()) {
            return true;
        }

        $message = sprintf('%s '.$this->translator->trans('bitbag_sylius_wishlist_plugin.ui.does_not_have_sufficient_stock'), $cartItem->getProductName());

        $this->flashBag->add('error', $message);


        return false;
    }
}
