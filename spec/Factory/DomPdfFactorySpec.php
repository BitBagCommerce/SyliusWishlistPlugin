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

namespace spec\Sylius\WishlistPlugin\Factory;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Factory\DomPdfFactory;
use Sylius\WishlistPlugin\Factory\DomPdfFactoryInterface;
use Sylius\WishlistPlugin\Factory\DomPdfOptionsFactoryInterface;

final class DomPdfFactorySpec extends ObjectBehavior
{
    public function let(
        DomPdfOptionsFactoryInterface $domPdfOptionsFactory,
    ): void {
        $this->beConstructedWith($domPdfOptionsFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DomPdfFactory::class);
        $this->shouldImplement(DomPdfFactoryInterface::class);
    }

    public function it_creates_new_dom_pdf(): void
    {
        $domPdf = $this->createNew();
        $domPdf->shouldBeAnInstanceOf(Dompdf::class);
    }

    public function it_creates_new_dom_pdf_with_default_options(
        DomPdfOptionsFactoryInterface $domPdfOptionsFactory,
        Options $pdfOptions,
    ): void {
        $domPdfOptionsFactory->createNew()->willReturn($pdfOptions);

        $pdfOptions->set('isRemoteEnabled', true)->shouldBeCalled();
        $pdfOptions->set('defaultFont', 'Arial')->shouldBeCalled();
        $pdfOptions->getHttpContext()->willReturn(['http' => []]);

        $domPdf = $this->createNewWithDefaultOptions();
        $domPdf->shouldBeAnInstanceOf(Dompdf::class);
        $domPdf->getOptions()->shouldReturn($pdfOptions);
    }
}
