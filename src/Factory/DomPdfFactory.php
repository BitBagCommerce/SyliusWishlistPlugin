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

namespace Sylius\WishlistPlugin\Factory;

use Dompdf\Dompdf;

class DomPdfFactory implements DomPdfFactoryInterface
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
