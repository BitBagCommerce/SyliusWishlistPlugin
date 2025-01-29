<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\RemoveWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\RemoveWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;

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
