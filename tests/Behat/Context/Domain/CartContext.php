<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;

final class CartContext implements Context
{
    /**
     * @Then I should have :arg1 product in my cart
     */
    public function iShouldHaveProductInMyCart(string $productName): void
    {
    }
}
