<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsvInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ExportWishlistToCsvHandler implements MessageHandlerInterface
{
    private const CSV_HEADERS = [
        'variantId',
        'productId',
        'variantCode',
    ];

    private CsvWishlistProductFactoryInterface $csvWishlistProductFactory;

    private CsvSerializerFactoryInterface $csvSerializerFactory;

    public function __construct(
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvSerializerFactoryInterface $csvSerializerFactory
    ) {
        $this->csvWishlistProductFactory = $csvWishlistProductFactory;
        $this->csvSerializerFactory = $csvSerializerFactory;
    }

    public function __invoke(ExportWishlistToCsvInterface $exportWishlistToCsv): \SplFileObject
    {
        $wishlistProducts = $exportWishlistToCsv->getWishlistProducts();
        $file = $exportWishlistToCsv->getFile();

        return $this->putDataToCsv($wishlistProducts, $file);
    }

    private function putDataToCsv(Collection $wishlistProducts, \SplFileObject $file): \SplFileObject
    {
        $file->fputcsv(self::CSV_HEADERS);

        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $csvWishlistProduct = $this->createCsvWishlistProduct($wishlistProduct);

            $file->fputcsv($this->csvSerializerFactory->createNew()->normalize($csvWishlistProduct, 'csv'));
        }

        return $file;
    }

    private function createCsvWishlistProduct(WishlistItemInterface $wishlistProduct): CsvWishlistProductInterface
    {
        return $this->csvWishlistProductFactory->createWithProperties(
            $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId(),
            $wishlistProduct->getWishlistProduct()->getProduct()->getId(),
            $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getCode(),
        );
    }
}
