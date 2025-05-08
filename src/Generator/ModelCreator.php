<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Generator;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModelInterface;
use Sylius\WishlistPlugin\Resolver\VariantImageToDataUriResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class ModelCreator implements ModelCreatorInterface
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
