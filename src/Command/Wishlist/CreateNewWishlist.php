<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateNewWishlist
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private FormFactoryInterface $formFactory;

    public function __construct(TokenStorageInterface $tokenStorage, WishlistFactoryInterface $wishlistFactory, FormFactoryInterface $formFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->formFactory = $formFactory;
    }

    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function getWishlistFactory(): WishlistFactoryInterface
    {
        return $this->wishlistFactory;
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}