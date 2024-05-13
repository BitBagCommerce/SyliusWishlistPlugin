<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CsvWishlistProductFactoryInterface extends FactoryInterface
{
    public function createWithProperties(
        int $variantId,
        int $productId,
        string $variantCode,
    ): CsvWishlistProductInterface;
}
