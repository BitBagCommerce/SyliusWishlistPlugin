<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Doctrine\Common\Collections\Collection;

interface ExportSelectedProductsFromWishlistToPdfInterface
{
    public function getWishlistProducts(): ?Collection;
}
