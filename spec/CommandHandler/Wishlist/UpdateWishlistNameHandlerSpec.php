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

use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Command\Wishlist\UpdateWishlistName;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\UpdateWishlistNameHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\WishlistNameIsTakenException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;

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
