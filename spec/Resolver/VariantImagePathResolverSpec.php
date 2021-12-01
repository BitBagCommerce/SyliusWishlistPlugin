<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Resolver\VariantImagePathResolver;
use Doctrine\Common\Collections\Collection;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;


final class VariantImagePathResolverSpec extends ObjectBehavior
{
    private const TEST_BASE_URL = 'http://test:8000';
    private const TEST_IMAGE_PATH = 'test/path/image.jpg';

    function let(CacheManager $cacheManager): void
    {
        $this->beConstructedWith(
            $cacheManager,
            $rootPath = 'project/path'

        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantImagePathResolver::class);
    }

    function it_resolve_empty_image_path(
        ProductVariantInterface $variant,
        ProductInterface $product,
        Collection $productImages
    ):  void {
        $variant->getProduct()->willReturn($product);
        $product->getImages()->willReturn($productImages);
        $productImages->first()->willReturn(false);

        $this->resolve($variant, self::TEST_BASE_URL)->shouldReturn('');
    }

    function it_resolve_image_path(
        ProductVariantInterface $variant,
        ProductInterface $product,
        Collection $productImages,
        ProductImage $productImage
    ):  void {

        $variant->getProduct()->willReturn($product);
        $product->getImages()->willReturn($productImages);
        $productImages->first()->willReturn($productImage);
        $productImage->getPath()->willReturn(self::TEST_IMAGE_PATH);
    }
}
