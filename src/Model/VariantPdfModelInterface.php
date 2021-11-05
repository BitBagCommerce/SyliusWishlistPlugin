<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model;

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
