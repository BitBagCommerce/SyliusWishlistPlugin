<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use Symfony\Component\Form\FormInterface;

final class RemoveSelectedProductsFromWishlistAction extends BaseWishlistProductsAction
{
    protected function handleCommand(FormInterface $form): void
    {
        $command = new RemoveSelectedProductsFromWishlist($form->get('items')->getData());
        $this->messageBus->dispatch($command);
    }
}
