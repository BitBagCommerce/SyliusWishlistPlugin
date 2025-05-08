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

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use Sylius\WishlistPlugin\Exporter\WishlistToPdfExporterInterface;
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
