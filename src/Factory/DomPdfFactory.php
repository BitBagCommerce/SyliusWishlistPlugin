<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use Dompdf\Dompdf;

readonly class DomPdfFactory implements DomPdfFactoryInterface
{
    public function __construct(
        private DomPdfOptionsFactoryInterface $domPdfOptionsFactory,
    ) {
    }

    public function createNew(): Dompdf
    {
        return new Dompdf();
    }

    public function createNewWithDefaultOptions(): Dompdf
    {
        $pdfOptions = $this->domPdfOptionsFactory->createNew();

        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('defaultFont', 'Arial');

        $domPdf = $this->createNew();
        $domPdf->setOptions($pdfOptions);

        return $domPdf;
    }
}
