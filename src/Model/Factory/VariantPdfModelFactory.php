<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model\Factory;

use BitBag\SyliusWishlistPlugin\Model\VariantPdfModel;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantPdfModelFactory implements VariantPdfModelFactoryInterface
{
    public function createWithVariantAndImagePath
    (
        ProductVariantInterface $variant,
        string $path,
        int $quantity,
        string $actualVariant
    ):  VariantPdfModel
    {
        $productPdfModel = new VariantPdfModel();

        $productPdfModel->setvariant($variant);
        $productPdfModel->setImagePath($path);
        $productPdfModel->setQuantity($quantity);
        $productPdfModel->setActualVariant($actualVariant);

        return $productPdfModel;
    }
}