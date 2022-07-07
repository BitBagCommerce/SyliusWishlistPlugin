<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Helper;

use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProduct;
use BitBag\SyliusWishlistPlugin\Validator\CsvWishlistProductValidatorInterface;
use Gedmo\Exception\UploadableInvalidMimeTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

final class GetDataFromFile
{
    private CsvSerializerFactoryInterface $csvSerializerFactory;

    private CsvWishlistProductValidatorInterface $csvWishlistProductValidator;

    private array $allowedMimeTypes;

    public function __construct(
        CsvSerializerFactoryInterface $csvSerializerFactory,
        CsvWishlistProductValidatorInterface $csvWishlistProductValidator,
        array $allowedMimeTypes
    ) {
        $this->csvSerializerFactory = $csvSerializerFactory;
        $this->csvWishlistProductValidator = $csvWishlistProductValidator;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    public function getDataFromFile(\SplFileInfo $fileInfo, Request $request): void
    {
        if (!$this->fileIsValidMimeType($fileInfo)) {
            throw new UploadableInvalidMimeTypeException();
        }

        $csvData = file_get_contents((string) $fileInfo);

        $csvWishlistProducts = $this->csvSerializerFactory->createNew()->deserialize($csvData, sprintf('%s[]', CsvWishlistProduct::class), 'csv', [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
            CsvEncoder::AS_COLLECTION_KEY => true,
        ]);

        /** @var CsvWishlistProduct $csvWishlistProduct */
        foreach ($csvWishlistProducts as $csvWishlistProduct) {
            if ($this->csvWishlistProductValidator->csvWishlistProductIsValid($csvWishlistProduct)) {
                $variantIdRequestAttributes[] = $csvWishlistProduct->getVariantId();
                $request->attributes->set('variantId', $variantIdRequestAttributes);
            }
        }
    }

    private function fileIsValidMimeType(\SplFileInfo $fileInfo): bool
    {
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);

        return in_array($finfo->file($fileInfo->getRealPath()), $this->allowedMimeTypes);
    }
}
