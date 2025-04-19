<?php

/* 
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistProductNotFoundException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RemoveSelectedProductsFromWishlistHandler
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private EntityManagerInterface $wishlistProductManager,
    ) {
    }

    public function __invoke(RemoveSelectedProductsFromWishlist $removeSelectedProductsFromWishlistCommand): void
    {
        $this->removeSelectedProductsFromWishlist($removeSelectedProductsFromWishlistCommand->getWishlistProducts());
    }

    private function removeSelectedProductsFromWishlist(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $this->removeProductFromWishlist($wishlistProduct);
        }
    }

    private function removeProductFromWishlist(WishlistItemInterface $wishlistItem): void
    {
        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $wishlistItem->getWishlistProduct();

        if (null === $wishlistProduct) {
            throw new WishlistProductNotFoundException();
        }

        $productVariant = $this->productVariantRepository->find($wishlistProduct->getVariant());

        if (null === $productVariant) {
            throw new ProductNotFoundException();
        }

        $this->wishlistProductManager->remove($wishlistProduct);
    }
}
