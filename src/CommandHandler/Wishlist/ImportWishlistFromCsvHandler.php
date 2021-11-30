<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsv;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistAction;
use Gedmo\Exception\UploadableInvalidMimeTypeException;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportWishlistFromCsvHandler implements MessageHandlerInterface
{
    private AddProductVariantToWishlistAction $addProductVariantToWishlistAction;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private array $allowedMimeTypes;

    public function __construct(
        AddProductVariantToWishlistAction $addProductVariantToWishlistAction,
        ProductVariantRepositoryInterface $productVariantRepository,
        array $allowedMimeTypes
    ) {
        $this->addProductVariantToWishlistAction = $addProductVariantToWishlistAction;
        $this->productVariantRepository = $productVariantRepository;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    public function __invoke(ImportWishlistFromCsv $importWishlistFromCsv): Response
    {
        $fileInfo = $importWishlistFromCsv->getFileInfo();
        $request = $importWishlistFromCsv->getRequest();

        $this->getDataFromFile($fileInfo, $request);

        return $this->addProductVariantToWishlistAction->__invoke($request);
    }

    private function getDataFromFile(\SplFileInfo $fileInfo, Request $request): void
    {
        $requestData = [];

        if (!$this->fileIsValidMimeType($fileInfo)) {
            throw new UploadableInvalidMimeTypeException();
        }

        $resource = fopen($fileInfo->getRealPath(), 'r');

        while ($data = fgetcsv($resource, 1000, ',')) {
            if ($this->csvContainValidProduct($data)) {
                $requestData[] = $data[0];
                $request->attributes->set('variantId', $requestData);
            }
        }
        fclose($resource);
    }

    private function fileIsValidMimeType(\SplFileInfo $fileInfo): bool
    {
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);

        return in_array($finfo->file($fileInfo->getRealPath()), $this->allowedMimeTypes);
    }

    private function csvContainValidProduct(array $data): bool
    {
        if (!array_diff(['0', '1', '2'], array_keys($data))) {
            $variantId = $data[0];
            $productId = $data[1];
            $variantCode = $data[2];

            $variant = $this->productVariantRepository->find($variantId);

            if (null === $variant) {
                throw new NotFoundHttpException();
            }

            if ((string) $variant->getProduct()->getId() === $productId && $variant->getCode() === $variantCode) {
                return true;
            }
        }

        return false;
    }
}
