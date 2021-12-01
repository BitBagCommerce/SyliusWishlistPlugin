<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use BitBag\SyliusWishlistPlugin\Exception\SelectAtLeastOneProductException;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProduct;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ExportWishlistToCsvHandler implements MessageHandlerInterface
{
    private NormalizerInterface $normalizer;

    private int $itemsProcessed = 0;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function __invoke(ExportWishlistToCsv $exportWishlistToCsv): \SplFileObject
    {
        $wishlistProducts = $exportWishlistToCsv->getWishlistProducts();
        $file = $exportWishlistToCsv->getFile();

        $fileObject = $this->putDataToCsv($wishlistProducts, $file);

        if (0 === $this->itemsProcessed) {
            throw new SelectAtLeastOneProductException();
        }

        return $fileObject;
    }

    private function putDataToCsv(Collection $wishlistProducts, \SplFileObject $file): \SplFileObject
    {
        $csvHeaders = [
          'variantId',
          'productId',
          'variantCode',
        ];

        $file->fputcsv($csvHeaders);

        /** @var AddWishlistProduct $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if (!$wishlistProduct->isSelected()) {
                continue;
            }
            $csvWishlistProduct = new CsvWishlistProduct();
            $csvWishlistProduct->setVariantId($wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId());
            $csvWishlistProduct->setProductId($wishlistProduct->getWishlistProduct()->getProduct()->getId());
            $csvWishlistProduct->setVariantCode($wishlistProduct->getCartItem()->getCartItem()->getVariant()->getCode());
            $file->fputcsv($this->normalizer->normalize($csvWishlistProduct, 'csv'));

            ++$this->itemsProcessed;
        }

        return $file;
    }
}
