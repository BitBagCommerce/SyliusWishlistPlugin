<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

interface ExportSelectedProductsFromWishlistToPdfInterface
{
    public function getWishlistProducts(): ?ArrayCollection;

    public function getRequest(): Request;
}
