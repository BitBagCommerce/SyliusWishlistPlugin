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

namespace Tests\Sylius\WishlistPlugin\Behat\Context\Common;

use Behat\Behat\Context\Context;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    public function __construct(private WishlistRepositoryInterface $wishlistRepository)
    {
    }

    /**
     * @When there are :count wishlists in the database
     * @When there is :count wishlist in the database
     */
    public function thereAreWishlistsInTheDatabase(int $count): void
    {
        Assert::same(count($this->wishlistRepository->findAll()), $count);
    }
}
