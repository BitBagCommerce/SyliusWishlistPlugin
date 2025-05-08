<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Controller\Action;

use Sylius\WishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class RemoveSelectedProductsFromWishlistAction extends BaseWishlistProductsAction
{
    protected function handleCommand(FormInterface $form): void
    {
        $command = new RemoveSelectedProductsFromWishlist($form->getData());

        try {
            $this->messageBus->dispatch($command);
            $this->getFlashBag()->add('success', $this->translator->trans('sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));
        } catch (HandlerFailedException) {
            $this->getFlashBag()->add('error', $this->translator->trans('sylius_wishlist_plugin.ui.wishlist_product_not_found'));
        }
    }
}
