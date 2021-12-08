<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Resolver\GenerateDataUriForImageResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImageToDataUriResolver;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantImageToDataUriResolverSpec extends ObjectBehavior
{
    private const TEST_BASE_URL = 'http://test:8000';

    public function let(GenerateDataUriForImageResolverInterface $dataUriForImageResolver): void
    {
        $this->beConstructedWith(
            $dataUriForImageResolver
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantImageToDataUriResolver::class);
    }

    public function it_resolve_empty_image_path(
        ProductVariantInterface $variant,
        ProductInterface $product,
        Collection $productImages
    ): void {
        $variant->getProduct()->willReturn($product);
        $product->getImages()->willReturn($productImages);
        $productImages->first()->willReturn(false);

        $this->resolve($variant, self::TEST_BASE_URL)->shouldReturn('');
    }

    public function it_resolve_image_path(
        ProductVariantInterface $variant,
        ProductInterface $product,
        Collection $productImages,
        ProductImage $productImage,
        GenerateDataUriForImageResolverInterface $dataUriForImageResolver
    ): void {
        $variant->getProduct()->willReturn($product);
        $product->getImages()->willReturn($productImages);
        $productImages->first()->willReturn($productImage);
        $dataUriForImageResolver->resolve($productImage)->willReturn(self::TEST_BASE_URL);
        $this->resolve($variant, self::TEST_BASE_URL)->shouldReturn(self::TEST_BASE_URL);
    }
}
