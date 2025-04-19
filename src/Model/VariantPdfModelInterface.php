<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
