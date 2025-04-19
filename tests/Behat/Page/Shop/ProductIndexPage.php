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

namespace Tests\Sylius\WishlistPlugin\Behat\Page\Shop;

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
