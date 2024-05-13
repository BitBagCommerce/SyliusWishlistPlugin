<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
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
