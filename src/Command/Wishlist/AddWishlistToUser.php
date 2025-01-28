<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final readonly class AddWishlistToUser implements WishlistSyncCommandInterface
{
    public function __construct(
        private WishlistInterface $wishlist,
        private ShopUserInterface $shopUser,
    ) {
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
