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

namespace Sylius\WishlistPlugin\Exporter;

use Sylius\WishlistPlugin\Factory\DomPdfFactoryInterface;
use Doctrine\Common\Collections\Collection;
use Twig\Environment;

final class DomPdfWishlistExporter implements DomPdfWishlistExporterInterface
{
    public function __construct(
        private Environment $twigEnvironment,
        private DomPdfFactoryInterface $domPdfFactory,
    ) {
    }

    public function export(Collection $data): void
    {
        $dompdf = $this->domPdfFactory->createNewWithDefaultOptions();
        $html = $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/_wishlist_pdf.html.twig', [
            'title' => 'My wishlist products',
            'date' => date('d.m.Y'),
            'products' => $data,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Wishlist', ['Attachment' => true]);
    }
}
