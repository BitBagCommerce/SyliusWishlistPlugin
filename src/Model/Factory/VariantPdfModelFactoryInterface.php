<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model\Factory;

use BitBag\SyliusWishlistPlugin\Model\VariantPdfModel;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface VariantPdfModelFactoryInterface
{
    public function createWithVariantAndImagePath
    (
        ProductVariantInterface $variant,
        string $path,
        int $quantity,
        string $actualVariant
    ):  VariantPdfModel;
}