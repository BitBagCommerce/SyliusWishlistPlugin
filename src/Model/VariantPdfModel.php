<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Model;

use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantPdfModel implements VariantPdfModelInterface
{
    private ProductVariantInterface $variant;

    private string $imagePath;

    private int $quantity;

    private string $actualVariant;

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
