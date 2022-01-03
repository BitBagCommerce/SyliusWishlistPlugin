<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

interface WishlistsResolverInterface
{
    public function resolve(): ?array;
}
