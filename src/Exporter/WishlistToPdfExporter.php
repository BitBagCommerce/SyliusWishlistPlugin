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

use Doctrine\Common\Collections\Collection;
use Sylius\WishlistPlugin\Processor\VariantPdfModelProcessorInterface;

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
