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
            ->getOneOrNullResult()
        ;
    }
}
