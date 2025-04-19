<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface VariantImageToDataUriResolverInterface
{
    public const IMAGE_PATH_IN_PROJECT = '/public/media/image/';

    public function resolve(ProductVariantInterface $variant, string $baseUrl): string;
}
