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
