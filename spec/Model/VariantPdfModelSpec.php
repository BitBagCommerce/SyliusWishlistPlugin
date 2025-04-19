<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Model;

use Sylius\WishlistPlugin\Model\VariantPdfModel;
use Sylius\WishlistPlugin\Model\VariantPdfModelInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class VariantPdfModelSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantPdfModel::class);
    }

    public function it_implements_variant_pdf_model_interface(): void
    {
        $this->shouldHaveType(VariantPdfModelInterface::class);
    }

    public function it_returns_property_of_variant_pdf_model(ProductVariantInterface $productVariant): void
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
