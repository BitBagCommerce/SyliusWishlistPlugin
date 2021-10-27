<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model;

use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantPdfModel implements VariantPdfModelInterface
{
    /** @var ProductVariantInterface */
    private $variant;

    /** @var string */
    private $imagePath;

    /** @var int */
    private $quantity;

    /** @var string */
    private $actualVariant;

    public function getVariant(): ProductVariantInterface
    {
        return $this->variant;
    }

    public function setVariant(ProductVariantInterface $variant): void
    {
        $this->variant = $variant;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getActualVariant(): string
    {
        return $this->actualVariant;
    }

    public function setActualVariant(string $actualVariant): void
    {
        $this->actualVariant = $actualVariant;
    }
}