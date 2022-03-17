<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Exporter;

use Doctrine\Common\Collections\Collection;

interface WishlistToPdfExporterInterface
{
    public function createModelToPdfAndExportToPdf(Collection $wishlistProducts): void;
}
