<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Factory;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface WishlistFactoryInterface extends FactoryInterface
{
    public function createForUser(ShopUserInterface $shopUser): WishlistInterface;

    public function createForUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): WishlistInterface;
}
