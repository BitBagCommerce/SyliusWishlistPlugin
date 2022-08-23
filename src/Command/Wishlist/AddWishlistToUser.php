<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class AddWishlistToUser
{
    private WishlistInterface $wishlist;

    private ShopUserInterface $shopUser;

    public function __construct(
        WishlistInterface $wishlist,
        ShopUserInterface $shopUser
    ) {
        $this->wishlist = $wishlist;
        $this->shopUser = $shopUser;
    }

    public function getWishlist(): WishlistInterface
    {
        return $this->wishlist;
    }

    public function getShopUser(): ShopUserInterface
    {
        return $this->shopUser;
    }
}
