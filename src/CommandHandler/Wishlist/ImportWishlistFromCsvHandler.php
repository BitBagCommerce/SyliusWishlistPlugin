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
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProduct;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Gedmo\Exception\UploadableInvalidMimeTypeException;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ImportWishlistFromCsvHandler implements MessageHandlerInterface
{
    private AddProductVariantToWishlistAction $addProductVariantToWishlistAction;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private DecoderInterface $decoder;

    private DenormalizerInterface $denormalizer;

    private array $allowedMimeTypes;

    public function __construct(
        AddProductVariantToWishlistAction $addProductVariantToWishlistAction,
        ProductVariantRepositoryInterface $productVariantRepository,
        DecoderInterface $serializer,
        array $allowedMimeTypes,
        DenormalizerInterface $denormalizer
    ) {
        $this->addProductVariantToWishlistAction = $addProductVariantToWishlistAction;
        $this->productVariantRepository = $productVariantRepository;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->decoder = $serializer;
        $this->denormalizer = $denormalizer;
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
        if (!$this->fileIsValidMimeType($fileInfo)) {
            throw new UploadableInvalidMimeTypeException();
        }

        $wishlistProductsArray = $this->decoder->decode(file_get_contents((string) $fileInfo), 'csv');

        foreach ($wishlistProductsArray as $wishlistProductArray) {
            /** @var CsvWishlistProduct $csvWishlistProduct */
            $csvWishlistProduct = $this->denormalizer->denormalize($wishlistProductArray, CsvWishlistProduct::class, 'csv');

            if (!$this->csvWishlistProductIsValid($csvWishlistProduct)) {
                return;
            }
            $variantIdRequestAttributes[] = $csvWishlistProduct->getVariantId();
        }

        $request->attributes->set('variantId', $variantIdRequestAttributes);
    }

    private function fileIsValidMimeType(\SplFileInfo $fileInfo): bool
    {
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);

        return in_array($finfo->file($fileInfo->getRealPath()), $this->allowedMimeTypes);
    }

    private function csvWishlistProductIsValid(CsvWishlistProductInterface $csvWishlistProduct): bool
    {
        $wishlistProduct = $this->productVariantRepository->findOneBy([
            'id' => $csvWishlistProduct->getVariantId(),
            'product' => $csvWishlistProduct->getProductId(),
            'code' => $csvWishlistProduct->getVariantCode(),
        ]);

        dump($csvWishlistProduct);

        if (null === $wishlistProduct) {
            $message = sprintf(
                "ProductId: %s, variantId: %s, variantCode: %s",
                $csvWishlistProduct->getProductId(),
                $csvWishlistProduct->getVariantId(),
                $csvWishlistProduct->getVariantCode()
            );
            throw new NotFoundHttpException($message);
        }

        return true;
    }
}
