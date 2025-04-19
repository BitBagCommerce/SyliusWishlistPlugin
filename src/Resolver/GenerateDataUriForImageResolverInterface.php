<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Resolver;

use Sylius\Component\Core\Model\ImageInterface;

interface GenerateDataUriForImageResolverInterface
{
    public const PATH_TO_EMPTY_PRODUCT_IMAGE = 'bundles/bitbagsyliuswishlistplugin/images/SyliusLogo.png';

    public function resolve(ImageInterface $image): string;

    public function resolveWithNoImage(): string;
}
