<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantImagePathResolver implements VariantImagePathResolverInterface
{
    private CacheManager $cacheManager;

    private string $rootPath;

    public function __construct(CacheManager $cacheManager, string $rootPath)
    {
        $this->cacheManager = $cacheManager;
        $this->rootPath = $rootPath;
    }

    public function resolve(ProductVariantInterface $variant, string $baseUrl): string
    {
        if (false === $variant->getProduct()->getImages()->first()) {
            return '';
        }

        $image = $variant->getProduct()->getImages()->first()->getPath();
        $filePath = sprintf('%s%s%s',$this->rootPath, self::IMAGE_PATH_IN_PROJECT, $image);
        $type = pathinfo($image, PATHINFO_EXTENSION);
        $data = file_get_contents($filePath);
        $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $dataUri;
    }
}
