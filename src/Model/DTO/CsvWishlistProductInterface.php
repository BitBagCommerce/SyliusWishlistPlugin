<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Model\DTO;

interface CsvWishlistProductInterface
{
    public function getVariantId(): ?int;

    public function setVariantId(?int $variantId): void;

    public function getProductId(): ?int;

    public function setProductId(?int $productId): void;

    public function getVariantCode(): ?string;

    public function setVariantCode(?string $variantCode): void;
}
