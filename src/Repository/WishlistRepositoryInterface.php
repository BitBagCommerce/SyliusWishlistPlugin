<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Repository;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface WishlistRepositoryInterface extends RepositoryInterface
{
    public function findOneByShopUser(ShopUserInterface $shopUser): ?WishlistInterface;

    public function findByToken(string $token): ?WishlistInterface;

    public function findAllByToken(string $token): array;

    public function findAllByShopUser(int $shopUser): array;

    public function findAllByAnonymous(?string $token = null): array;

    public function findAllAnonymousUpdatedAtEarlierThan(\DateTimeInterface $updatedAt): array;

    public function findAllByShopUserAndToken(int $shopUser, string $token): array;

    public function findOneByShopUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): ?WishlistInterface;

    public function findAllByAnonymousAndChannel(?string $token, ChannelInterface $channel): array;

    public function findOneByTokenAndName(string $token, string $name): ?WishlistInterface;

    public function findOneByShopUserAndName(ShopUserInterface $shopUser, string $name): ?WishlistInterface;

    public function findAllByShopUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): array;
}
