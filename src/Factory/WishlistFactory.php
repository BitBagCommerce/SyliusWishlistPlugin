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

namespace Sylius\WishlistPlugin\Factory;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistFactory implements WishlistFactoryInterface
{
    public function __construct(
        private FactoryInterface $wishlistFactory,
    ) {
    }

    public function createNew(): WishlistInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();

        $wishlist->setName('Wishlist');

        return $wishlist;
    }

    public function createForUser(ShopUserInterface $shopUser): WishlistInterface
    {
        $wishlist = $this->createNew();

        $wishlist->setShopUser($shopUser);

        return $wishlist;
    }

    public function createForUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): WishlistInterface {
        $wishlist = $this->createNew();

        $wishlist->setChannel($channel);
        $wishlist->setShopUser($shopUser);

        return $wishlist;
    }
}
