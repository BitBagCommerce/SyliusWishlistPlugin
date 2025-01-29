<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Generator;

use BitBag\SyliusWishlistPlugin\Command\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModelInterface;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImageToDataUriResolverInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final readonly class ModelCreator implements ModelCreatorInterface
{
    public function __construct(
        private VariantImageToDataUriResolverInterface $variantImageToDataUriResolver,
        private VariantPdfModelFactoryInterface $variantPdfModelFactory,
        private RequestStack $requestStack,
    ) {
    }

    public function createWishlistItemToPdf(WishlistItemInterface $wishlistItem): VariantPdfModelInterface
    {
        /** @var ?AddToCartCommandInterface $cartItemCommand */
        $cartItemCommand = $wishlistItem->getCartItem();
        Assert::notNull($cartItemCommand);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $cartItemCommand->getCartItem();
        $variant = $cartItem->getVariant();
        Assert::notNull($variant);
        $quantity = $cartItem->getQuantity();

        /** @var ?Request $request */
        $request = $this->requestStack->getCurrentRequest();
        Assert::notNull($request);

        $baseUrl = $request->getSchemeAndHttpHost();
        $urlToImage = $this->variantImageToDataUriResolver->resolve($variant, $baseUrl);
        $variantCode = $variant->getCode();
        Assert::notNull($variantCode);

        return $this->variantPdfModelFactory->createWithVariantAndImagePath(
            $variant,
            $urlToImage,
            $quantity,
            $variantCode,
        );
    }
}
