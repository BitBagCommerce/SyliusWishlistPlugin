<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantImagePathResolver implements VariantImagePathResolverInterface
{
    public function resolve(ProductVariantInterface $variant, string $baseUrl)
    {
        $imagePath = $variant->getProduct()->getImages()->first()->getPath();
        return $baseUrl.'/media/image/'.$imagePath;
    }
}