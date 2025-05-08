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

namespace Tests\Sylius\WishlistPlugin\Behat\Service;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;

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
