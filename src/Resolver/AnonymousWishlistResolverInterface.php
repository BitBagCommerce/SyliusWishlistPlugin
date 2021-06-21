<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

interface AnonymousWishlistResolverInterface
{
    public function resolve(string $token): WishlistInterface;
}
