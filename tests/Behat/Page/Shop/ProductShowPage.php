<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Product\ShowPage;

class ProductShowPage extends ShowPage implements ProductShowPageInterface
{
    public function addVariantToWishlist(): void
    {
        $this->getDocument()->find('css', '.bitbag-add-variant-to-wishlist')->click();

        // Wait for the ajax request to finish
        $this->getSession()->wait(1000, 'typeof jQuery !== "undefined" && 0 === jQuery.active');
    }
}
