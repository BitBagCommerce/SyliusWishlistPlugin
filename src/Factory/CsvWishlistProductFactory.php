<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProduct;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;

final class CsvWishlistProductFactory implements CsvWishlistProductFactoryInterface
{
    public function createNew(): CsvWishlistProductInterface
    {
        /** @var CsvWishlistProductInterface $csvWishlistProduct */
        $csvWishlistProduct = new CsvWishlistProduct();

        return $csvWishlistProduct;
    }

    public function createWithProperties(
        int $variantId,
        int $productId,
        string $variantCode
    ): CsvWishlistProductInterface {
        /** @var CsvWishlistProductInterface $csvWishlistProduct */
        $csvWishlistProduct = $this->createNew();

        $csvWishlistProduct->setVariantId($variantId);
        $csvWishlistProduct->setProductId($productId);
        $csvWishlistProduct->setVariantCode($variantCode);

        return $csvWishlistProduct;
    }
}
