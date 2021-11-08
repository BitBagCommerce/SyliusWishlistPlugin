<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateNewWishlist
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    public function __construct(TokenStorageInterface $tokenStorage, WishlistFactoryInterface $wishlistFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
    }

    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function getWishlistFactory(): WishlistFactoryInterface
    {
        return $this->wishlistFactory;
    }
}