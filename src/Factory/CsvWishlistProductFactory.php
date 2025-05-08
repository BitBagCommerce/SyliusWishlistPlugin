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

namespace Sylius\WishlistPlugin\Factory;

use Sylius\WishlistPlugin\Model\DTO\CsvWishlistProduct;
use Sylius\WishlistPlugin\Model\DTO\CsvWishlistProductInterface;

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
