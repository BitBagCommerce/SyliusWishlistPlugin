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
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Command\Wishlist\AddWishlistToUser;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\AddWishlistToUserHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\WishlistHasAnotherShopUserException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;

final class AddWishlistToUserHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistCookieTokenResolver,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddWishlistToUserHandler::class);
    }

    public function it_adds_wishlist_to_user(
        WishlistInterface $wishlist,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ): void {
        $wishlistCookieTokenResolver->resolve()->willReturn('token');

        $wishlist->getToken()->willReturn('token');
        $wishlist->getName()->willReturn('Testing wishlist');
        $wishlist->getId()->willReturn(1);

        $wishlistRepository->findOneByShopUserAndName($shopUser, 'Testing wishlist')->willReturn($wishlist);
        $wishlist->setName('Testing wishlist1');
        $wishlist->setShopUser($shopUser)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $addWishlistToUser = new AddWishlistToUser($wishlist->getWrappedObject(), $shopUser->getWrappedObject());

        $this->__invoke($addWishlistToUser);
    }

    public function it_doesnt_add_wishlist_to_user_if_token_doesnt_match(
        WishlistInterface $wishlist,
        ShopUserInterface $shopUser,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ): void {
        $wishlistCookieTokenResolver->resolve()->willReturn('token');

        $wishlist->getToken()->willReturn('anotherToken');
        $wishlist->getName()->shouldNotBeCalled();
        $wishlist->getId()->shouldNotBeCalled();

        $wishlistRepository->findOneByShopUserAndName($shopUser, 'name')->shouldNotBeCalled();
        $wishlist->setShopUser($shopUser)->shouldNotBeCalled();
        $wishlistRepository->add($wishlist)->shouldNotBeCalled();

        $addWishlistToUser = new AddWishlistToUser($wishlist->getWrappedObject(), $shopUser->getWrappedObject());

        $this
            ->shouldThrow(WishlistHasAnotherShopUserException::class)
            ->during('__invoke', [$addWishlistToUser]);
    }
}
