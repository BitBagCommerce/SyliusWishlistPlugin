<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Exporter;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;

interface ExporterWishlistToPdfInterface
{
    public function createModelToPdfAndExportToPdf(Collection $wishlistProducts, Request $request): void;
}