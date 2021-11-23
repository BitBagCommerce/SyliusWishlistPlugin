<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

final class ExportSelectedProductsFromWishlistToPdf implements ExportSelectedProductsFromWishlistToPdfInterface
{
    private Request $request;
    private ArrayCollection $wishlistProducts;

    public function __construct(ArrayCollection $wishlistProducts, Request $request)
    {
        $this->wishlistProducts = $wishlistProducts;
        $this->request = $request;
    }

    public function getWishlistProducts(): ?ArrayCollection
    {
        return $this->wishlistProducts;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
