<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Checker;

use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductQuantityChecker implements ProductQuantityCheckerInterface
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

    public function productHasPositiveQuantity(OrderItemInterface $product): bool
    {
        if (0 < $product->getQuantity()) {
            return true;
        }
        $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));

        return false;
    }
}