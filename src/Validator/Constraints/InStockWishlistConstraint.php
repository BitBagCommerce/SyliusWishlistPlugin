<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class InStockWishlistConstraint extends Constraint
{
    public string $message = 'sylius.cart_item.not_available';

    public const ADD_PRODUCTS_ROUTE = 'bitbag_sylius_wishlist_plugin_shop_locale_wishlist_add_selected_products';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_wishlist_plugin_validator_wishlist_in_stock_validator';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
