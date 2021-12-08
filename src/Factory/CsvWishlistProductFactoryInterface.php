<?php

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CsvWishlistProductFactoryInterface extends FactoryInterface
{
    public function createWithProperties(
        int $variantId,
        int $productId,
        string $variantCode
    ): CsvWishlistProductInterface;
}
