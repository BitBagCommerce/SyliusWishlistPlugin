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
