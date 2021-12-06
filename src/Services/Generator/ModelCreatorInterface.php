<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Generator;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModelInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\HttpFoundation\Request;

interface ModelCreatorInterface
{
    public function createWishlistItemToPdf(
        WishlistItemInterface $wishlistProduct,
        Request $request,
        ProductVariant $variant
    ): VariantPdfModelInterface;
}
