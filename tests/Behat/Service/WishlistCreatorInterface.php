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

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

interface WishlistCreatorInterface
{
    public function createWishlistWithProductAndUser(
        ShopUserInterface $shopUser,
        ProductInterface $product,
        WishlistInterface $wishlist,
    ): void;
}
