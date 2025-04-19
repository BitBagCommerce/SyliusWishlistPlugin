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

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\ExportSelectedProductsFromWishlistToPdfHandler;
use Sylius\WishlistPlugin\Exporter\WishlistToPdfExporterInterface;
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
