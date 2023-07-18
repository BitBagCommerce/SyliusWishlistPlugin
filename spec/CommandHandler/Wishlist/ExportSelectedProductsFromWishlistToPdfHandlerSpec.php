<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\ExportSelectedProductsFromWishlistToPdfHandler;
use BitBag\SyliusWishlistPlugin\Services\Exporter\WishlistToPdfExporterInterface;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;

final class ExportSelectedProductsFromWishlistToPdfHandlerSpec extends ObjectBehavior
{
    public function let(WishlistToPdfExporterInterface $exporterWishlistToPdf): void
    {
        $this->beConstructedWith($exporterWishlistToPdf);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportSelectedProductsFromWishlistToPdfHandler::class);
    }

    public function it_exports_a_collection_of_products(
        WishlistToPdfExporterInterface $exporterWishlistToPdf,
        ExportSelectedProductsFromWishlistToPdfInterface $exportSelectedProductsFromWishlistToPdf,
        Collection $wishlistProducts
    ): void {
        $exportSelectedProductsFromWishlistToPdf
            ->getWishlistProducts()
            ->willReturn($wishlistProducts);

        $exporterWishlistToPdf
            ->createModelToPdfAndExportToPdf($wishlistProducts)
            ->shouldBeCalled();

        $this->__invoke($exportSelectedProductsFromWishlistToPdf);
    }
}
