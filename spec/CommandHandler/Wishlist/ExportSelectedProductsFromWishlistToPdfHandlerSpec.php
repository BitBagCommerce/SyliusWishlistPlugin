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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ExportSelectedProductsFromWishlistToPdfHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistToPdfExporterInterface $exporterWishlistToPdf
    ): void
    {
        $this->beConstructedWith(
            $exporterWishlistToPdf
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportSelectedProductsFromWishlistToPdfHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_exports_products_from_wishlist_to_pdf(
        ExportSelectedProductsFromWishlistToPdfInterface $exportSelectedProductsFromWishlistToPdf,
        WishlistToPdfExporterInterface $exporter,
        Collection $collection
    ): void
    {
//        $wishlistProducts = new ArrayCollection();
//        $exportSelectedProductsFromWishlistToPdf->getWishlistProducts()->willReturn($wishlistProducts);
//        $exportSelectedProductsFromWishlistToPdf->getWishlistProducts()->willReturn($collection);

//        $exporter->createModelToPdfAndExportToPdf($wishlistProducts)->shouldBeCalled();
//        $exporter->createModelToPdfAndExportToPdf($collection->getWrappedObject())->shouldBeCalled();
//
//        $this->__invoke($exportSelectedProductsFromWishlistToPdf);
    }
}



//use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
//use BitBag\SyliusWishlistPlugin\Services\Exporter\WishlistToPdfExporterInterface;
//use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
//
//final class ExportSelectedProductsFromWishlistToPdfHandler implements MessageHandlerInterface
//{
//    public function __invoke(ExportSelectedProductsFromWishlistToPdfInterface $exportSelectedProductsFromWishlistToPdf): void
//    {
//        $wishlistProducts = $exportSelectedProductsFromWishlistToPdf->getWishlistProducts();
//        $this->exporterWishlistToPdf
//            ->createModelToPdfAndExportToPdf($wishlistProducts);
//    }
//}
