<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Command\Wishlist;

final class RemoveProductVariantFromWishlist implements WishlistSyncCommandInterface
{
    public function __construct(
        private int $productVariantId,
        private string $wishlistToken,
    ) {
    }

    public function getProductVariantIdValue(): int
    {
        return $this->productVariantId;
    }

    public function getWishlistTokenValue(): string
    {
        return $this->wishlistToken;
    }
}
