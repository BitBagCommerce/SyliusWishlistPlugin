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
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ExportWishlistToCsvHandler implements MessageHandlerInterface
{
    private int $itemsProcessed = 0;

    public function __invoke(ExportWishlistToCsv $exportWishlistToCsv): ?\SplFileObject
    {
        $wishlistProducts = $exportWishlistToCsv->getWishlistProducts();
        $file = $exportWishlistToCsv->getFile();

        $this->putDataToCsv($wishlistProducts, $file);

        if (0 === $this->itemsProcessed) {
            throw new SelectAtLeastOneProductException();
        }

        return $file;
    }

    private function putDataToCsv(Collection $wishlistProducts, \SplFileObject $file): void
    {
        /** @var AddWishlistProduct $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if (!$wishlistProduct->isSelected()) {
                continue;
            }
            $csvWishlistItem = [
                    $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId(),
                    $wishlistProduct->getWishlistProduct()->getProduct()->getId(),
                    $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getCode(),
                ];
            $file->fputcsv($csvWishlistItem);
            ++$this->itemsProcessed;
        }
    }
}
