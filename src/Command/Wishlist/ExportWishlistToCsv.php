<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

final class ExportWishlistToCsv
{
    private array $wishlistProducts;

    /** @var resource|false */
    private $file;

    public function __construct(array $wishlistProducts)
    {
        $this->wishlistProducts = $wishlistProducts;
    }

    public function getWishlistProducts(): array
    {
        return $this->wishlistProducts;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): void
    {
        if (is_resource($file)) {
            $this->file = $file;
        }
    }
}
