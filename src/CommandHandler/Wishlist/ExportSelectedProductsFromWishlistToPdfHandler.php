<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\Services\Exporter\WishlistToPdfExporterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ExportSelectedProductsFromWishlistToPdfHandler implements MessageHandlerInterface
{
    private RequestStack $request;

    private WishlistToPdfExporterInterface $exporterWishlistToPdf;

    public function __construct(
        RequestStack $request,
        WishlistToPdfExporterInterface $exporterWishlistToPdf
    ) {
        $this->request = $request;
        $this->exporterWishlistToPdf = $exporterWishlistToPdf;
    }

    public function __invoke(ExportSelectedProductsFromWishlistToPdfInterface $exportSelectedProductsFromWishlistToPdf): void
    {
        $wishlistProducts = $exportSelectedProductsFromWishlistToPdf->getWishlistProducts();
        $this->exporterWishlistToPdf
            ->createModelToPdfAndExportToPdf($wishlistProducts, $this->request->getCurrentRequest());
    }
}
