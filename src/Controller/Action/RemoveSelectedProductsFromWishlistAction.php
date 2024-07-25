<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class RemoveSelectedProductsFromWishlistAction extends BaseWishlistProductsAction
{
    protected function handleCommand(FormInterface $form): void
    {
        $command = new RemoveSelectedProductsFromWishlist($form->getData());

        try {
            $this->messageBus->dispatch($command);
            $this->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));
        } catch (HandlerFailedException) {
            $this->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_product_not_found'));
        }
    }
}
