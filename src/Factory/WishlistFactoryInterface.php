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

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;

interface WishlistFactoryInterface extends FactoryInterface
{
    public function createForUser(ShopUserInterface $shopUser): WishlistInterface;

    public function createForUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): WishlistInterface;
}
