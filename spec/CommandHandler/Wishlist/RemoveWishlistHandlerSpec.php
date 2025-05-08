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

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Command\Wishlist\RemoveWishlist;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\RemoveWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;

final class RemoveWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager,
    ): void {
        $this->beConstructedWith($wishlistRepository, $wishlistManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveWishlistHandler::class);
    }

    public function it_removes_matching_wishlist(
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager,
        WishlistInterface $wishlist,
    ): void {
        $removeWishlist = new RemoveWishlist('token');

        $wishlistRepository->findByToken('token')->willReturn($wishlist);

        $wishlistManager->remove($wishlist)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeWishlist);
    }

    public function it_throws_exception_when_wishlist_isnt_found(
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager,
        WishlistInterface $wishlist,
    ): void {
        $removeWishlist = new RemoveWishlist('token');

        $wishlistRepository->findByToken('token')->willReturn(null);

        $wishlistManager->remove($wishlist)->shouldNotBeCalled();
        $wishlistManager->flush()->shouldNotBeCalled();

        $this
            ->shouldThrow(WishlistNotFoundException::class)
            ->during('__invoke', [$removeWishlist]);
    }
}
