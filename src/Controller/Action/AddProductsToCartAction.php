<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
