<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        string $variantCode,
    ): CsvWishlistProductInterface {
        /** @var CsvWishlistProductInterface $csvWishlistProduct */
        $csvWishlistProduct = $this->createNew();

        $csvWishlistProduct->setVariantId($variantId);
        $csvWishlistProduct->setProductId($productId);
        $csvWishlistProduct->setVariantCode($variantCode);

        return $csvWishlistProduct;
    }
}
