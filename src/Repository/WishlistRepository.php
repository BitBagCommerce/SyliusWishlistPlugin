<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Repository;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     */
    public function findOneByShopUser(ShopUserInterface $shopUser): ?WishlistInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByToken(string $token): ?WishlistInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findAllByToken(string $token): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByShopUser(int $shopUser): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByShopUserAndToken(int $shopUser, string $token): array
    {
        $qb = $this->createQueryBuilder('o');

        return $qb->where('o.shopUser = :shopUser')
            ->orWhere($qb->expr()->andX(
                'o.token = :token',
                'o.shopUser IS NULL',
            ))
            ->setParameter('token', $token)
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByAnonymous(?string $token): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.token = :token')
            ->andWhere('o.shopUser IS NULL')
            ->setParameter('token', $token)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByShopUserAndChannel(
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): ?WishlistInterface {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->andWhere('o.channel = :channel')
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
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.shopUser IS NULL')
            ->setParameter('channel', $channel)
        ;

        if (null !== $token) {
            $qb
                ->andWhere('o.token = :token')
                ->setParameter('token', $token);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneByTokenAndName(string $token, string $name): ?WishlistInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.token = :token')
            ->andWhere('o.name =:name')
            ->setParameter('token', $token)
            ->setParameter('name', $name)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findOneByShopUserAndName(ShopUserInterface $shopUser, string $name): ?WishlistInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->andWhere('o.name =:name')
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
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->andWhere('o.channel = :channel')
            ->setParameter('shopUser', $shopUser)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;
    }
}
