<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Symfony\Component\HttpFoundation\Request;

final class ImportWishlistFromCsv
{
    private \SplFileInfo $file;

    private Request $request;

    private int $wishlistId;

    public function __construct(
        \SplFileInfo $file,
        Request $request,
        int $wishlistId
    ) {
        $this->file = $file;
        $this->request = $request;
        $this->wishlistId = $wishlistId;
    }

    public function getFileInfo(): \SplFileInfo
    {
        return $this->file;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getWishlistId(): int
    {
        return $this->wishlistId;
    }
}
