<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ProductImageInterface;

interface GenerateDataUriForImageResolverInterface
{
    public const PATH_TO_EMPTY_PRODUCT_IMAGE = 'bundles/bitbagsyliuswishlistplugin/images/SyliusLogo.png';

    public function resolve(ProductImageInterface $image): string;

    public function resolveWithNoImage(): string;
}
