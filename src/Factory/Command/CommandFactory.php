<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory\Command;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdf;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

final class CommandFactory implements CommandFactoryInterface
{
    public function createFrom(ArrayCollection $wishlistProducts, Request $request): ExportSelectedProductsFromWishlistToPdfInterface
    {
        return new ExportSelectedProductsFromWishlistToPdf($wishlistProducts,$request);
    }
}
