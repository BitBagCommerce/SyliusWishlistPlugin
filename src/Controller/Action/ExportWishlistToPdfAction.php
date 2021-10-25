<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class ExportWishlistToPdfAction
{
    private Environment $environment;
    private WishlistContextInterface $wishlistContext;

    public function __construct(Environment $environment, WishlistContextInterface $wishlistContext)
    {
        $this->environment = $environment;
        $this->wishlistContext = $wishlistContext;
    }

    public function __invoke(Request $request)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->environment->render('@BitBagSyliusWishlistPlugin/_wishlist_pdf.html.twig', [
            'title' => "Welcome to our PDF Test"
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("wishlist.pdf", [
            "Attachment" => true
        ]);
    }


}