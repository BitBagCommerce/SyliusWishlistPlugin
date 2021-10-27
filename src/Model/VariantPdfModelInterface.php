<?php

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