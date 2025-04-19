<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class VariantImageToDataUriResolver implements VariantImageToDataUriResolverInterface
{
    public function __construct(
        private GenerateDataUriForImageResolverInterface $dataUriForImageResolver,
    ) {
    }

    public function resolve(ProductVariantInterface $variant, string $baseUrl): string
    {
        /** @var ?ProductInterface $product */
        $product = $variant->getProduct();
        Assert::notNull($product);

        $image = $product->getImages()->first();

        if (false === $image) {
            return $this->dataUriForImageResolver->resolveWithNoImage();
        }

        $fileExt = explode('.', (string) $image->getPath());

        if ('svg' === $fileExt[1]) {
            return $this->dataUriForImageResolver->resolveWithNoImage();
        }

        return $this->dataUriForImageResolver->resolve($image);
    }
}
