<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface VariantImagePathResolverInterface
{
    public function resolve(ProductVariantInterface $variant, string $baseUrl);
}