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
use Symfony\Component\HttpFoundation\RequestStack;

final class ModelCreator implements ModelCreatorInterface
{
    public function __construct(
        private VariantImageToDataUriResolverInterface $variantImageToDataUriResolver,
        private VariantPdfModelFactoryInterface $variantPdfModelFactory,
        private RequestStack $requestStack
    ) {
    }

    public function createWishlistItemToPdf(WishlistItemInterface $wishlistProduct): VariantPdfModelInterface
    {
        $cartItem = $wishlistProduct->getCartItem()->getCartItem();
        $variant = $cartItem->getVariant();
        $quantity = $cartItem->getQuantity();
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
        $urlToImage = $this->variantImageToDataUriResolver->resolve($variant, $baseUrl);
        $variantCode = $variant->getCode();

        return $this->variantPdfModelFactory->createWithVariantAndImagePath(
            $variant,
            $urlToImage,
            $quantity,
            $variantCode
        );
    }
}
