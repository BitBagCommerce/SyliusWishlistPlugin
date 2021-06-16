<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

interface UserWishlistResolverInterface
{
    public function resolve(ShopUserInterface $user): WishlistInterface;
}
