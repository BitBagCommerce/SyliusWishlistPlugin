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

namespace Sylius\WishlistPlugin\Generator;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModelInterface;

interface ModelCreatorInterface
{
    public function createWishlistItemToPdf(WishlistItemInterface $wishlistItem): VariantPdfModelInterface;
}
