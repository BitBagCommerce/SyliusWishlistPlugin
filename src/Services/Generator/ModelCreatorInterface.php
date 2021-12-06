<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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
