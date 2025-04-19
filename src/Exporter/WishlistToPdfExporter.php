<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Exporter;

use Sylius\WishlistPlugin\Processor\VariantPdfModelProcessorInterface;
use Doctrine\Common\Collections\Collection;

final class WishlistToPdfExporter implements WishlistToPdfExporterInterface
{
    public function __construct(
        private VariantPdfModelProcessorInterface $variantPdfModelProcessor,
        private DomPdfWishlistExporterInterface $domPdfWishlistExporter,
    ) {
    }

    public function createModelToPdfAndExportToPdf(Collection $wishlistProducts): void
    {
        $productsToExport = $this->variantPdfModelProcessor->createVariantPdfModelCollection($wishlistProducts);

        $this->domPdfWishlistExporter->export($productsToExport);
    }
}
