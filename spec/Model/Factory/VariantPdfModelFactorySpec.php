<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Model\Factory;

use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactory;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModel;
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
            'variant test'
        );

        $productPdfModel->getVariant()->shouldReturn($productVariant);
        $productPdfModel->getImagePath()->shouldReturn('http://127.0.0.1:8000/media/image/b4/c2/fc6b3202ee567e0fb05f293b709c.jpg');
        $productPdfModel->getQuantity()->shouldReturn(10);
        $productPdfModel->getActualVariant()->shouldReturn('variant test');

        $this->createWithVariantAndImagePath(
            $productVariant,
            'http://127.0.0.1:8000/media/image/b4/c2/fc6b3202ee567e0fb05f293b709c.jpg',
            10,
            'variant test'
        )->shouldBeAnInstanceOf(VariantPdfModel::class);
    }
}
