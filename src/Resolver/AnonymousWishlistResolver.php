<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class AnonymousWishlistResolver implements AnonymousWishlistResolverInterface
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

    public function resolve(string $token): WishlistInterface
    {
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (!$wishlist) {
            $wishlist = $this->wishlistFactory->createNew();
        }

        return $wishlist;
    }
}
