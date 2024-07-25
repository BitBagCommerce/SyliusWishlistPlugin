<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistSyncCommandInterface;
use Symfony\Component\Form\FormInterface;

final class AddSelectedProductsToCartAction extends BaseAddWishlistProductsAction
{
    protected function getCommand(FormInterface $form): WishlistSyncCommandInterface
    {
        return new AddSelectedProductsToCart($form->getData());
    }
}
