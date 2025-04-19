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
