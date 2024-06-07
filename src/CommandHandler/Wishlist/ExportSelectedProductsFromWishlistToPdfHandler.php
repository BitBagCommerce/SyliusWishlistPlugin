<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\Exporter\WishlistToPdfExporterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ExportSelectedProductsFromWishlistToPdfHandler
{
    public function __construct(
        private WishlistToPdfExporterInterface $exporterWishlistToPdf,
    ) {
    }

    public function __invoke(ExportSelectedProductsFromWishlistToPdfInterface $exportSelectedProductsFromWishlistToPdf): void
    {
        $wishlistProducts = $exportSelectedProductsFromWishlistToPdf->getWishlistProducts();
        $this->exporterWishlistToPdf
            ->createModelToPdfAndExportToPdf($wishlistProducts);
    }
}
