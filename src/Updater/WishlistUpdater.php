<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Updater;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\Persistence\ObjectManager;

final class WishlistUpdater implements WishlistUpdaterInterface
{
    private ObjectManager $wishlistManager;

    public function __construct(ObjectManager $wishlistManager)
    {
        $this->wishlistManager = $wishlistManager;
    }

    public function updateWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();
    }

    public function addProductToWishlist(WishlistInterface $wishlist, WishlistProductInterface $product): WishlistInterface
    {
        $wishlist->addWishlistProduct($product);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }

}
