<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistFactory implements WishlistFactoryInterface
{
    private FactoryInterface $wishlistFactory;

    public function __construct(FactoryInterface $wishlistFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
    }

    public function createNew(): WishlistInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();

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
        ChannelInterface $channel
    ): WishlistInterface {
        $wishlist = $this->createNew();

        $wishlist->setChannel($channel);
        $wishlist->setShopUser($shopUser);

        return $wishlist;
    }
}
