<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Factory;

use Sylius\WishlistPlugin\Factory\DomPdfFactory;
use Sylius\WishlistPlugin\Factory\DomPdfFactoryInterface;
use Sylius\WishlistPlugin\Factory\DomPdfOptionsFactoryInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpSpec\ObjectBehavior;

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
