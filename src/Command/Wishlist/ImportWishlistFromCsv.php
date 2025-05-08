<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Command\Wishlist;

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
