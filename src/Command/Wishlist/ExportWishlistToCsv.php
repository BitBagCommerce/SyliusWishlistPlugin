<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Doctrine\Common\Collections\Collection;

final class ExportWishlistToCsv
{
    private Collection $wishlistProducts;

    private \SplFileObject $file;

    public function __construct(Collection $wishlistProducts, \SplFileObject $file)
    {
        $this->wishlistProducts = $wishlistProducts;
        $this->file = $file;
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
