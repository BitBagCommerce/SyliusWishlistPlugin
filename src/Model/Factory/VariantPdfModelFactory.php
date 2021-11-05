<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model\Factory;

use BitBag\SyliusWishlistPlugin\Model\VariantPdfModel;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantPdfModelFactory implements VariantPdfModelFactoryInterface
{
    public function createWithVariantAndImagePath(
        ProductVariantInterface $variant,
        string $path,
        int $quantity,
        string $actualVariant
    ):  VariantPdfModel
    {
        $productPdfModel = new VariantPdfModel();

        $productPdfModel->setVariant($variant);
        $productPdfModel->setImagePath($path);
        $productPdfModel->setQuantity($quantity);
        $productPdfModel->setActualVariant($actualVariant);

        return $productPdfModel;
    }
}