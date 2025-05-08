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

namespace Sylius\WishlistPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    public function findOneByShopUser(ShopUserInterface $shopUser): ?WishlistInterface
    {
        return $this->createQueryBuilder('w')
            ->where('w.shopUser = :shopUser')
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findByToken(string $token): ?WishlistInterface
    {
        return $this->createQueryBuilder('w')
            ->where('w.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findAllByToken(string $token): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByShopUser(int $shopUser): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.shopUser = :shopUser')
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByShopUserAndToken(int $shopUser, string $token): array
    {
        $qb = $this->createQueryBuilder('w');

        return $qb->where('w.shopUser = :shopUser')
            ->orWhere($qb->expr()->andX(
                'w.token = :token',
                'w.shopUser IS NULL',
            ))
            ->setParameter('token', $token)
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByAnonymous(?string $token = null): array
    {
        $qb = $this->createQueryBuilder('w')
            ->andWhere('w.shopUser IS NULL')
        ;

        if (null !== $token) {
            $qb
                ->andWhere('w.token = :token')
                ->setParameter('token', $token);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllAnonymousUpdatedAtEarlierThan(\DateTimeInterface $updatedAt): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.shopUser IS NULL')
            ->andWhere('w.updatedAt <= :updatedAt')
            ->setParameter('updatedAt', $updatedAt)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByShopUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): ?WishlistInterface {
        return $this->createQueryBuilder('w')
            ->where('w.shopUser = :shopUser')
            ->andWhere('w.channel = :channel')
            ->setParameter('shopUser', $shopUser)
            ->setParameter('channel', $channel)
            ->setMaxResults(1)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findAllByAnonymousAndChannel(?string $token, ChannelInterface $channel): array
    {
        $qb = $this->createQueryBuilder('w')
            ->andWhere('w.channel = :channel')
            ->andWhere('w.shopUser IS NULL')
            ->setParameter('channel', $channel)
        ;

        if (null !== $token) {
            $qb
                ->andWhere('w.token = :token')
                ->setParameter('token', $token);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneByTokenAndName(string $token, string $name): ?WishlistInterface
    {
        return $this->createQueryBuilder('w')
            ->where('w.token = :token')
            ->andWhere('w.name =:name')
            ->setParameter('token', $token)
            ->setParameter('name', $name)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findOneByShopUserAndName(ShopUserInterface $shopUser, string $name): ?WishlistInterface
    {
        return $this->createQueryBuilder('w')
            ->where('w.shopUser = :shopUser')
            ->andWhere('w.name =:name')
            ->setParameter('shopUser', $shopUser)
            ->setParameter('name', $name)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findAllByShopUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): array {
        return $this->createQueryBuilder('w')
            ->where('w.shopUser = :shopUser')
            ->andWhere('w.channel = :channel')
            ->setParameter('shopUser', $shopUser)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;
    }
}
