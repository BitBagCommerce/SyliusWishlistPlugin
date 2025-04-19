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

namespace spec\Sylius\WishlistPlugin\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Checker\WishlistNameChecker;

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
