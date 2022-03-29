<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Repository;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    public function findOneByShopUser(ShopUserInterface $shopUser): ?WishlistInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

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

    public function findAllByShopUser(int $shopUser): ?array
    {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByShopUserAndToken(int $shopUser, string $token): ?array
    {
        return $this->createQueryBuilder('o')
            ->where('o.shopUser = :shopUser')
            ->orWhere('o.token = :token')
            ->setParameter('token', $token)
            ->setParameter('shopUser', $shopUser)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByAnonymous(?string $token): ?array
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
        ChannelInterface $channel
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

    public function findAllByAnonymousAndChannel(?string $token, ChannelInterface $channel): ?array
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
}
