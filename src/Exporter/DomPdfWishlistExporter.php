<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
