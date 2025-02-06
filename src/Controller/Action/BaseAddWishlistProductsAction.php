<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface;
use BitBag\SyliusWishlistPlugin\Exception\InsufficientProductStockException;
use BitBag\SyliusWishlistPlugin\Exception\InvalidProductQuantityException;
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
                $this->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
            }
        } catch (HandlerFailedException $exception) {
            $this->getFlashBag()->add('error', $this->getExceptionMessage($exception));
        }
    }

    private function getExceptionMessage(HandlerFailedException $exception): string
    {
        $previous = $exception->getPrevious();
        if ($previous instanceof InsufficientProductStockException) {
            return $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.insufficient_stock', ['%productName%' => $previous->getProductName()]);
        }
        if ($previous instanceof InvalidProductQuantityException) {
            return $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity');
        }

        return $exception->getMessage();
    }
}
