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

use Sylius\WishlistPlugin\Command\Wishlist\AddProductsToCart;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface;
use Symfony\Component\Form\FormInterface;

final class AddProductsToCartAction extends BaseAddWishlistProductsAction
{
    protected function getCommand(FormInterface $form): WishlistSyncCommandInterface
    {
        return new AddProductsToCart($form->get('items')->getData());
    }
}
