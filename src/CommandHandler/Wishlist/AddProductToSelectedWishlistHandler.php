<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
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
