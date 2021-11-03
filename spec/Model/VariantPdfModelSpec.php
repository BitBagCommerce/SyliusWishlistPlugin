<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Model;

use BitBag\SyliusWishlistPlugin\Model\VariantPdfModel;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModelInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantPdfModelSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantPdfModel::class);
    }

    function it_implements_variant_pdf_model_interface(): void
    {
        $this->shouldHaveType(VariantPdfModelInterface::class);
    }

    function it_returns_property_of_variant_pdf_model(ProductVariantInterface $productVariant): void
    {
        $this->setActualVariant('variant test');
        $this->setVariant($productVariant);
        $this->setImagePath('/image/123/image.jpg');
        $this->setQuantity(10);

        $this->getActualVariant()->shouldReturn('variant test');
        $this->getVariant()->shouldReturn($productVariant);
        $this->getImagePath()->shouldReturn('/image/123/image.jpg');
        $this->getQuantity()->shouldReturn(10);
   }
}