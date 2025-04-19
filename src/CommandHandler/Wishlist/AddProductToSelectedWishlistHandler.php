<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
