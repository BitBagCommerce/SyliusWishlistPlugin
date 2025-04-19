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

namespace spec\Sylius\WishlistPlugin\Model\Factory;

use Sylius\WishlistPlugin\Model\Factory\VariantPdfModelFactory;
use Sylius\WishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModel;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariant;

final class VariantPdfModelFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantPdfModelFactory::class);
    }

    public function it_implements_variant_pdf_model_factory_interface(): void
    {
        $this->shouldHaveType(VariantPdfModelFactoryInterface::class);
    }

    public function it_returns_product_pdf_model(): void
    {
        $productVariant = new ProductVariant();
        $productPdfModel = $this->createWithVariantAndImagePath(
            $productVariant,
            'http://127.0.0.1:8000/media/image/b4/c2/fc6b3202ee567e0fb05f293b709c.jpg',
            10,
            'variant test',
        );

        $productPdfModel->getVariant()->shouldReturn($productVariant);
        $productPdfModel->getImagePath()->shouldReturn('http://127.0.0.1:8000/media/image/b4/c2/fc6b3202ee567e0fb05f293b709c.jpg');
        $productPdfModel->getQuantity()->shouldReturn(10);
        $productPdfModel->getActualVariant()->shouldReturn('variant test');

        $this->createWithVariantAndImagePath(
            $productVariant,
            'http://127.0.0.1:8000/media/image/b4/c2/fc6b3202ee567e0fb05f293b709c.jpg',
            10,
            'variant test',
        )->shouldBeAnInstanceOf(VariantPdfModel::class);
    }
}
