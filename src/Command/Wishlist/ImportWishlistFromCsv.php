<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Symfony\Component\HttpFoundation\Request;

final class ImportWishlistFromCsv implements WishlistSyncCommandInterface
{
    public function __construct(
        private \SplFileInfo $file,
        private Request $request,
        private int $wishlistId,
    ) {
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
