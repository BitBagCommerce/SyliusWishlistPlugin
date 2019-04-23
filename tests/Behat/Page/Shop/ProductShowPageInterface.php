<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop;

interface ProductShowPageInterface
{
    public function addVariantToWishlist(): void;
}
