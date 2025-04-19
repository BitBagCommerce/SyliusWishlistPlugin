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

namespace Sylius\WishlistPlugin\Resolver;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserWishlistResolver implements ShopUserWishlistResolverInterface
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistFactoryInterface $wishlistFactory,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function resolve(ShopUserInterface $user): WishlistInterface
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $channel = null;
        }

        if ($channel instanceof ChannelInterface) {
            return $this->wishlistRepository->findOneByShopUserAndChannel($user, $channel) ?? $this->wishlistFactory->createForUserAndChannel($user, $channel);
        }

        return $this->wishlistRepository->findOneByShopUser($user) ?? $this->wishlistFactory->createForUser($user);
    }
}
