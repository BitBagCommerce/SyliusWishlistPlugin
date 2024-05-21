<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
