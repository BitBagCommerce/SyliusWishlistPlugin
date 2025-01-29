<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\ExportSelectedProductsFromWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\ExportSelectedProductsFromWishlistToPdfHandler;
use BitBag\SyliusWishlistPlugin\Exporter\WishlistToPdfExporterInterface;
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
        Collection $wishlistProducts,
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
