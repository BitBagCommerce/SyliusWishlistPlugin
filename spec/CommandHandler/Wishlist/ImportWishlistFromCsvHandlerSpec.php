<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsvInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\ImportWishlistFromCsvHandler;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistActionInterface;
use BitBag\SyliusWishlistPlugin\Helper\GetDataFromFileInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportWishlistFromCsvHandlerSpec extends ObjectBehavior
{
    public function let(
        AddProductVariantToWishlistActionInterface $addProductVariantToWishlistAction,
        GetDataFromFileInterface $getDataFromFile
    ): void {
        $this->beConstructedWith(
            $addProductVariantToWishlistAction,
            $getDataFromFile
        );
    }

    public function it_is_initializable(): void {
        $this->shouldHaveType(ImportWishlistFromCsvHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_imports_wishlist_from_csv(
        ImportWishlistFromCsvInterface $importWishlistFromCsv,
        \SplFileInfo $fileInfo,
        Request $request,
        GetDataFromFileInterface $getDataFromFile,
        AddProductVariantToWishlistActionInterface $addProductVariantToWishlistAction
    ): void {
        $importWishlistFromCsv->getFileInfo()->willReturn($fileInfo);
        $importWishlistFromCsv->getRequest()->willReturn($request);
        $importWishlistFromCsv->getWishlistId()->willReturn(1);
        $addProductVariantToWishlistAction->__invoke(1, $request)->willReturn(new Response());

        $getDataFromFile->getDataFromFile($fileInfo, $request)->shouldBeCalled();

        $this->__invoke($importWishlistFromCsv)->shouldBeAnInstanceOf(Response::class);
    }
}
