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

use Sylius\WishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdf;
use Symfony\Component\Form\FormInterface;

final class ExportWishlistToPdfAction extends BaseWishlistProductsAction
{
    protected function handleCommand(FormInterface $form): void
    {
        $command = new ExportSelectedProductsFromWishlistToPdf($form->getData());
        $this->messageBus->dispatch($command);

        // Preventing downloads timing out. In HTTP proxies without that indicator a timeout will occur.
        exit();
    }
}
