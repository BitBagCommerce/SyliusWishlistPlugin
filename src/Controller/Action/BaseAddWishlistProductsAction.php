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

use Sylius\WishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface;
use Sylius\WishlistPlugin\Exception\InsufficientProductStockException;
use Sylius\WishlistPlugin\Exception\InvalidProductQuantityException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

abstract class BaseAddWishlistProductsAction extends BaseWishlistProductsAction
{
    abstract protected function getCommand(FormInterface $form): WishlistSyncCommandInterface;

    protected function handleCommand(FormInterface $form): void
    {
        $command = $this->getCommand($form);

        try {
            $this->messageBus->dispatch($command);
            if (false === $this->getFlashBag()->has('success')) {
                $this->getFlashBag()->add('success', $this->translator->trans('sylius_wishlist_plugin.ui.added_to_cart'));
            }
        } catch (HandlerFailedException $exception) {
            $this->getFlashBag()->add('error', $this->getExceptionMessage($exception));
        }
    }

    private function getExceptionMessage(HandlerFailedException $exception): string
    {
        $previous = $exception->getPrevious();
        if ($previous instanceof InsufficientProductStockException) {
            return $this->translator->trans('sylius_wishlist_plugin.ui.insufficient_stock', ['%productName%' => $previous->getProductName()]);
        }
        if ($previous instanceof InvalidProductQuantityException) {
            return $this->translator->trans('sylius_wishlist_plugin.ui.increase_quantity');
        }

        return $exception->getMessage();
    }
}
