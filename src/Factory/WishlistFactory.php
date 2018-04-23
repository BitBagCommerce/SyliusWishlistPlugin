<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistFactory implements WishlistFactoryInterface
{
    /** @var FactoryInterface */
    private $wishlistFactory;

    /** @var FactoryInterface */
    private $wishlistProductFactory;

    public function __construct(FactoryInterface $wishlistFactory, FactoryInterface $wishlistProductFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
    }

    public function createNew(): WishlistInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();
        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createNew();

        $wishlistProduct->setWishlist($wishlist);
        $wishlist->addWishlistProduct($wishlistProduct);

        return $wishlist;
    }

    public function createForUser(ShopUserInterface $shopUser): WishlistInterface
    {
        $wishlist = $this->createNew();

        $wishlist->setShopUser($shopUser);

        return $wishlist;
    }
}
