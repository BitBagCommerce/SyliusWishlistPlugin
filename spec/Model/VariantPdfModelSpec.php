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
