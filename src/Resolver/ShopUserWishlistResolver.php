<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

class ShopUserWishlistResolver implements ShopUserWishlistResolverInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistFactoryInterface $wishlistFactory;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
    }

    public function resolve(ShopUserInterface $user): WishlistInterface
    {
        $wishlist = $this->wishlistRepository->findByShopUser($user);

        if (!$wishlist) {
            $wishlist = $this->wishlistFactory->createForUser($user);
        }

        return $wishlist;
    }
}
