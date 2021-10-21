<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Product\IndexPage;

class ProductIndexPage extends IndexPage implements ProductIndexPageInterface
{
    public function addProductToWishlist(string $productName): void
    {
        $this->getSession()->setCookie('MOCKSESSID', 'foo');

        $wishlistElements = $this->getDocument()->findAll('css', '[data-test-wishlist-add-product]');

        /** @var NodeElement $wishlistElement */
        foreach ($wishlistElements as $wishlistElement) {
            if ($productName === $wishlistElement->getAttribute('data-product-name')) {
                $wishlistElement->click();

                return;
            }
        }
    }
}
