<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Checker;

use Sylius\WishlistPlugin\Checker\WishlistNameChecker;
use PhpSpec\ObjectBehavior;

final class WishlistNameCheckerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistNameChecker::class);
    }

    public function it_returns_true_if_names_are_equal(): void
    {
        $existingWishlistName = 'test1';
        $wishlistToCreate = 'test1';

        $this->check($existingWishlistName, $wishlistToCreate)->shouldReturn(true);
    }

    public function it_returns_false_if_names_are_not_equal(): void
    {
        $existingWishlistName = 'test1';
        $wishlistToCreate = 'test2';

        $this->check($existingWishlistName, $wishlistToCreate)->shouldReturn(false);
    }
}
