<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\Asset\PackageInterface;

final class GenerateDataUriForImageResolver implements GenerateDataUriForImageResolverInterface
{
    public function __construct(
        private PackageInterface $package,
        private FilterService $filterService,
        private string $imageFilterName
    ) {}

    public function resolve(ProductImageInterface $image): string
    {
        $pathToReadFile = $this->package->getUrl($image->getPath());
        $targetUrl = $this->filterService->getUrlOfFilteredImage($pathToReadFile, $this->imageFilterName);
        $data = file_get_contents($targetUrl);
        $type = pathinfo($image->getPath(), \PATHINFO_EXTENSION);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function resolveWithNoImage(): string
    {
        $pathToReadFile =  self::PATH_TO_EMPTY_PRODUCT_IMAGE;
        $data = file_get_contents($pathToReadFile);

        return 'data:image/' . 'png' . ';base64,' . base64_encode($data);
    }
}
