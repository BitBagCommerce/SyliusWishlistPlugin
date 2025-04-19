<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\Sylius\WishlistPlugin\Behat\Service;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class WishlistCreator implements WishlistCreatorInterface
{
    public function __construct(
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private RepositoryInterface $wishlistRepository,
    ) {
    }

    public function createWishlistWithProductAndUser(
        ShopUserInterface $shopUser,
        ProductInterface $product,
        WishlistInterface $wishlist,
    ): void {
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistRepository->add($wishlist);
    }
}
