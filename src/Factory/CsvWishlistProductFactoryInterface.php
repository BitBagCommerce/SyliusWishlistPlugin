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

use Sylius\WishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CsvWishlistProductFactoryInterface extends FactoryInterface
{
    public function createWithProperties(
        int $variantId,
        int $productId,
        string $variantCode,
    ): CsvWishlistProductInterface;
}
