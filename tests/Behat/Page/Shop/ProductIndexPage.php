<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Product\IndexPage;

class ProductIndexPage extends IndexPage implements ProductIndexPageInterface
{
    public function addProductToWishlist(string $productName): void
    {
        $wishlistElements = $this->getDocument()->findAll('css', '.bitbag-add-to-wishlist');

        /** @var NodeElement $wishlistElement */
        foreach ($wishlistElements as $wishlistElement) {
            if ($productName === $wishlistElement->getAttribute('data-product-name')) {
                $wishlistElement->click();

                return;
            }
        }
    }
}
