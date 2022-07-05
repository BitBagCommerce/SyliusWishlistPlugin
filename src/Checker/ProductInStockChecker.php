<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductInStockChecker
{
    private FlashBagInterface $flashBag;
    private TranslatorInterface $translator;

    public function __construct(
      FlashBagInterface $flashBag,
      TranslatorInterface $translator
    ) {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function isInStock(WishlistItemInterface $wishlistItem): bool
    {
        $cartItem = $wishlistItem->getCartItem()->getCartItem();

        if ($cartItem->getVariant()->isInStock()) {
            return true;
        }

        $message = sprintf(' "%s" does not have sufficient stock.', $cartItem->getProductName());
        $this->flashBag->add('error', $this->translator->trans($message));

        return false;
    }
}
