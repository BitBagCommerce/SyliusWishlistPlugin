<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\CsvSerializerFactoryInterface;
use Sylius\WishlistPlugin\Factory\CsvWishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class ExportWishlistToCsvHandler
{
    public const CSV_HEADERS = [
        'variantId',
        'productId',
        'variantCode',
    ];

    public function __construct(
        private CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        private CsvSerializerFactoryInterface $csvSerializerFactory,
    ) {
    }

    public function __invoke(ExportWishlistToCsv $exportWishlistToCsv): \SplFileObject
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
            $csvData = $this->csvSerializerFactory->createNew()->normalize($csvWishlistProduct, 'csv');
            Assert::isArray($csvData);

            $file->fputcsv($csvData);
        }

        return $file;
    }

    private function createCsvWishlistProduct(WishlistItemInterface $wishlistItem): CsvWishlistProductInterface
    {
        /** @var ?AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $wishlistItem->getCartItem();
        Assert::notNull($addToCartCommand);
        /** @var OrderItemInterface $cartItem */
        $cartItem = $addToCartCommand->getCartItem();
        /** @var ?ProductVariantInterface $variant */
        $variant = $cartItem->getVariant();
        Assert::notNull($variant);
        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $wishlistItem->getWishlistProduct();
        Assert::notNull($wishlistProduct);

        return $this->csvWishlistProductFactory->createWithProperties(
            $variant->getId(),
            $wishlistProduct->getProduct()->getId(),
            (string) $variant->getCode(),
        );
    }
}
