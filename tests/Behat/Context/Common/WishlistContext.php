<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Common;

use Behat\Behat\Context\Context;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
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
