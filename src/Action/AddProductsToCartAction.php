<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Action;

use BitBag\SyliusWishlistPlugin\Command\AddProductsToCart;
use Symfony\Component\Form\FormInterface;

final class AddProductsToCartAction extends BaseWishlistProductsAction
{
    protected function handleCommand(FormInterface $form): void
    {
        $command = new AddProductsToCart($form->get('items')->getData());
        $this->messageBus->dispatch($command);
    }
}
