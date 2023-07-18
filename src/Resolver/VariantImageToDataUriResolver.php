<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantImageToDataUriResolver implements VariantImageToDataUriResolverInterface
{
    public function __construct(
        private GenerateDataUriForImageResolverInterface $dataUriForImageResolver
    ) {}

    public function resolve(ProductVariantInterface $variant, string $baseUrl): string
    {
        $image = $variant->getProduct()->getImages()->first();

        if (false === $image) {
            return $this->dataUriForImageResolver->resolveWithNoImage();
        }

        $fileExt = explode('.', $image->getPath());

        if ($fileExt[1] === "svg") {
            return $this->dataUriForImageResolver->resolveWithNoImage();
        }

        return $this->dataUriForImageResolver->resolve($image);
    }
}
