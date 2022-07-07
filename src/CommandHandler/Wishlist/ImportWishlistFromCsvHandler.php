<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsvInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistActionInterface;
use BitBag\SyliusWishlistPlugin\Helper\GetDataFromFileInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportWishlistFromCsvHandler implements MessageHandlerInterface
{
    private AddProductVariantToWishlistActionInterface $addProductVariantToWishlistAction;

    private GetDataFromFileInterface $getDataFromFile;

    public function __construct(
        AddProductVariantToWishlistActionInterface $addProductVariantToWishlistAction,
        GetDataFromFileInterface $getDataFromFile
    ) {
        $this->addProductVariantToWishlistAction = $addProductVariantToWishlistAction;
        $this->getDataFromFile = $getDataFromFile;
    }

    public function __invoke(ImportWishlistFromCsvInterface $importWishlistFromCsv): Response
    {
        $fileInfo = $importWishlistFromCsv->getFileInfo();
        $request = $importWishlistFromCsv->getRequest();
        $wishlistId = $importWishlistFromCsv->getWishlistId();

        $this->getDataFromFile->getDataFromFile($fileInfo, $request);

        return $this->addProductVariantToWishlistAction->__invoke($wishlistId, $request);
    }
}
