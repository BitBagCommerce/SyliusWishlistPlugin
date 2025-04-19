<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Resolver;

use Sylius\WishlistPlugin\Resolver\GenerateDataUriForImageResolverInterface;
use Sylius\WishlistPlugin\Resolver\VariantImageToDataUriResolver;
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
            $dataUriForImageResolver,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantImageToDataUriResolver::class);
    }

    public function it_resolves_empty_image_path(
        ProductVariantInterface $variant,
        ProductInterface $product,
        Collection $productImages,
        GenerateDataUriForImageResolverInterface $dataUriForImageResolver,
    ): void {
        $variant->getProduct()->willReturn($product);
        $product->getImages()->willReturn($productImages);
        $productImages->first()->willReturn(false);
        $dataUriForImageResolver->resolveWithNoImage()->willReturn(self::TEST_BASE_URL);

        $this->resolve($variant, self::TEST_BASE_URL)->shouldReturn(self::TEST_BASE_URL);
    }

    public function it_resolves_image_path(
        ProductVariantInterface $variant,
        ProductInterface $product,
        Collection $productImages,
        ProductImage $productImage,
        GenerateDataUriForImageResolverInterface $dataUriForImageResolver,
    ): void {
        $variant->getProduct()->willReturn($product);
        $product->getImages()->willReturn($productImages);
        $productImages->first()->willReturn($productImage);
        $productImage->getPath()->willReturn('test.jpg');
        $dataUriForImageResolver->resolve($productImage)->willReturn(self::TEST_BASE_URL);

        $this->resolve($variant, self::TEST_BASE_URL)->shouldReturn(self::TEST_BASE_URL);
    }
}
