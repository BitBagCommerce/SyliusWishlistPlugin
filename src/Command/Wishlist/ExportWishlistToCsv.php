<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Doctrine\Common\Collections\Collection;

final class ExportWishlistToCsv implements WishlistSyncCommandInterface
{
    public function __construct(
        private Collection $wishlistProducts,
        private \SplFileObject $file,
    ) {
    }

    public function getWishlistProducts(): Collection
    {
        return $this->wishlistProducts;
    }

    public function getFile(): \SplFileObject
    {
        return $this->file;
    }
}
