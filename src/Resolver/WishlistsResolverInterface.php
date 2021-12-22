<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Symfony\Component\HttpFoundation\Request;

interface WishlistsResolverInterface
{
    public function resolve(Request $request): ?array;
}
