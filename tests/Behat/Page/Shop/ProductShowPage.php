<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Shop\Product\ShowPage;

class ProductShowPage extends ShowPage implements ProductShowPageInterface
{
    public function addVariantToWishlist(): void
    {
        /** @var ?NodeElement $addProductToWishlist */
        $addProductToWishlist = $this->getDocument()->find('css', '[data-test-wishlist-add-product]');

        if (null === $addProductToWishlist) {
            throw new ElementNotFoundException($this->getDriver());
        }

        $addProductToWishlist->click();

        // Wait for the ajax request to finish
        $this->getSession()->wait(5000, 'document.querySelectorAll("[data-test-flash-messages]").length > 0');
    }
}
