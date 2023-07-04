<?php
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Checker\WishlistNameChecker;
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
