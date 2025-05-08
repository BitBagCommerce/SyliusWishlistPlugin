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

namespace Tests\Sylius\WishlistPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Product\IndexPageInterface;

interface ProductIndexPageInterface extends IndexPageInterface
{
    public function addProductToWishlist(string $productName): void;
}
