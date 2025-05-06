<?php

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Processor;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface AddProductVariantToWishlistProcessorInterface
{
    public function process(ProductVariantInterface $productVariant, ?int $wishlistId = null): RedirectResponse;
}
