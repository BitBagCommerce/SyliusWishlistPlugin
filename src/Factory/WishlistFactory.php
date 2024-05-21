<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
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
