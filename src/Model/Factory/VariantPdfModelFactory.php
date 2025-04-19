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

namespace Sylius\WishlistPlugin\Model\Factory;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModel;
use Sylius\WishlistPlugin\Model\VariantPdfModelInterface;

final class VariantPdfModelFactory implements VariantPdfModelFactoryInterface
{
    public function createWithVariantAndImagePath(
        ProductVariantInterface $variant,
        string $path,
        int $quantity,
        string $actualVariant,
    ): VariantPdfModelInterface {
        $productPdfModel = new VariantPdfModel();

        $productPdfModel->setVariant($variant);
        $productPdfModel->setImagePath($path);
        $productPdfModel->setQuantity($quantity);
        $productPdfModel->setActualVariant($actualVariant);

        return $productPdfModel;
    }
}
