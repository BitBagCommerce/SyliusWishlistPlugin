<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Updater;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class WishlistUpdater implements WishlistUpdaterInterface
{
    private ObjectManager $wishlistManager;

    private ObjectManager $wishlistProductManager;

    public function __construct(
        ObjectManager $wishlistManager,
        ObjectManager $wishlistProductManager
    ) {
        $this->wishlistManager = $wishlistManager;
        $this->wishlistProductManager = $wishlistProductManager;
    }

    public function updateWishlist(WishlistInterface $wishlist): void
    {
        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();
    }

    public function addProductToWishlist(WishlistInterface $wishlist, WishlistProductInterface $product): WishlistInterface
    {
        $wishlist->addWishlistProduct($product);
        $this->updateWishlist($wishlist);

        return $wishlist;
    }

    public function removeProductFromWishlist(WishlistInterface $wishlist, WishlistProductInterface $product): WishlistInterface
    {
        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($product === $wishlistProduct->getProduct()) {
                $this->wishlistProductManager->remove($wishlistProduct);
            }
        }

        $this->wishlistProductManager->flush();

        return $wishlist;
    }

    public function removeProductVariantFromWishlist(WishlistInterface $wishlist, ProductVariantInterface $variant): WishlistInterface
    {
        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($variant === $wishlistProduct->getVariant()) {
                $this->wishlistProductManager->remove($wishlistProduct);
            }
        }

        $this->wishlistProductManager->flush();

        return $wishlist;
    }
}
