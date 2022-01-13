<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Product\ShowPage;

class ProductShowPage extends ShowPage implements ProductShowPageInterface
{
    public function addVariantToWishlist(): void
    {
        $this->getDocument()->find('css', '[data-test-wishlist-add-product]')->click();

        // Wait for the ajax request to finish
        $this->getSession()->wait(5000, 'document.querySelectorAll("[data-test-flash-messages]").length > 0');
    }
}
