<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use http\Env\Response;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WishlistExtension extends AbstractExtension
{
    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(

        WishlistRepositoryInterface $wishlistRepository

    ) {
        $this->wishlistRepository = $wishlistRepository;

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('injectWishlists', [$this, 'injectWishlists']),
        ];
    }

    public function injectWishlists()
    {
        /** @var WishlistInterface $wishlists */
        $wishlists = $this->wishlistRepository->findAll();

        return $wishlists;
    }
}