<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\Sylius\WishlistPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Product\IndexPageInterface;

interface ProductIndexPageInterface extends IndexPageInterface
{
    public function addProductToWishlist(string $productName): void;
}
