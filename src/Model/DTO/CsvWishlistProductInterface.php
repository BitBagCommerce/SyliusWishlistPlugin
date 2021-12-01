<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model\DTO;

interface CsvWishlistProductInterface
{
    public function getVariantId(): ?int;

    public function setVariantId(?int $variantId): void;

    public function getProductId(): ?int;

    public function setProductId(?int $productId): void;

    public function getVariantCode(): ?string;

    public function setVariantCode(?string $variantCode): void;
}
