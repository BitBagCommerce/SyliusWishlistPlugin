<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\CommandHandler\UpdateWishlistNameHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;

final class UpdateWishlistNameHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ): void {
        $this->beConstructedWith($wishlistRepository, $wishlistCookieTokenResolver);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateWishlistNameHandler::class);
    }

    public function it_renames_found_wishlist(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        WishlistInterface $wishlist,
    ): void {
        $updateWishlistName = new UpdateWishlistName('newName', $wishlist->getWrappedObject());

        $wishlistCookieTokenResolver->resolve()->willReturn('token');

        $wishlistRepository->findOneByTokenAndName('token', 'newName')->willReturn(null);

        $wishlist->setName('newName')->shouldBeCalled();

        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $this->__invoke($updateWishlistName);
    }

    public function it_throws_exception_when_wishlist_name_is_already_taken(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        WishlistInterface $wishlist,
        WishlistInterface $existingWishlist,
    ): void {
        $updateWishlistName = new UpdateWishlistName('newName', $wishlist->getWrappedObject());

        $wishlistCookieTokenResolver->resolve()->willReturn('token');

        $wishlistRepository->findOneByTokenAndName('token', 'newName')->willReturn($wishlist);

        $wishlist->setName('newName')->shouldNotBeCalled();

        $wishlistRepository->add($wishlist)->shouldNotBeCalled();

        $this
            ->shouldThrow(WishlistNameIsTakenException::class)
            ->during('__invoke', [$updateWishlistName]);
    }
}
