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

use Sylius\WishlistPlugin\Command\Wishlist\AddProductToSelectedWishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddProductToSelectedWishlistHandler
{
    public function __construct(
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private WishlistRepositoryInterface $wishlistRepository,
    ) {
    }

    public function __invoke(AddProductToSelectedWishlistInterface $addProductToSelectedWishlist): void
    {
        $product = $addProductToSelectedWishlist->getProduct();
        $wishlist = $addProductToSelectedWishlist->getWishlist();

        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);
    }
}
