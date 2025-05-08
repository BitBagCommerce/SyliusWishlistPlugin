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

namespace Sylius\WishlistPlugin\Model;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface VariantPdfModelInterface
{
    public function getVariant(): ProductVariantInterface;

    public function setVariant(ProductVariantInterface $variant): void;

    public function getImagePath(): string;

    public function setImagePath(string $imagePath): void;

    public function getQuantity(): int;

    public function setQuantity(int $quantity): void;

    public function getActualVariant(): string;

    public function setActualVariant(string $actualVariant): void;
}
