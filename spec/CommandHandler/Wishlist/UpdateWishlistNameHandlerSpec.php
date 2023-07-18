<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\UpdateWishlistNameHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;

final class UpdateWishlistNameHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
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
        WishlistInterface $wishlist
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
        WishlistInterface $existingWishlist
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
