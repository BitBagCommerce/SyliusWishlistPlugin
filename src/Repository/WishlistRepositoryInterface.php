<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Repository;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface WishlistRepositoryInterface extends RepositoryInterface
{
    public function findOneByShopUser(ShopUserInterface $shopUser): ?WishlistInterface;

    public function findByToken(string $token): ?WishlistInterface;

    public function findAllByToken(string $token): ?array;

    public function findAllByShopUser(int $shopUser): ?array;

    public function findAllByAnonymous(?string $token): ?array;

    public function findAllByShopUserAndToken(int $shopUser, string $token): ?array;

    public function findOneByShopUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel
    ): ?WishlistInterface;

    public function findAllByAnonymousAndChannel(?string $token, ChannelInterface $channel): ?array;
}
