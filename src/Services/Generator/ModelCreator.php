<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Generator;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModelInterface;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImageToDataUriResolverInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\HttpFoundation\Request;

final class ModelCreator implements ModelCreatorInterface
{
    private VariantImageToDataUriResolverInterface $variantImageToDataUriResolver;

    private VariantPdfModelFactoryInterface $variantPdfModelFactory;

    public function __construct(
        VariantImageToDataUriResolverInterface $variantImageToDataUriResolver,
        VariantPdfModelFactoryInterface $variantPdfModelFactory
    ) {
        $this->variantImageToDataUriResolver = $variantImageToDataUriResolver;
        $this->variantPdfModelFactory = $variantPdfModelFactory;
    }

    public function createWishlistItemToPdf(
        WishlistItemInterface $wishlistProduct,
        Request $request,
        ProductVariant $variant
    ): VariantPdfModelInterface
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();
        $quantity = $cartItem->getQuantity();
        $baseUrl = $request->getSchemeAndHttpHost();
        $urlToImage = $this->variantImageToDataUriResolver->resolve($variant, $baseUrl);
        $actualVariant = $cartItem->getVariant()->getCode();

        return $this->variantPdfModelFactory->createWithVariantAndImagePath(
            $variant,
            $urlToImage,
            $quantity,
            $actualVariant
        );
    }
}
