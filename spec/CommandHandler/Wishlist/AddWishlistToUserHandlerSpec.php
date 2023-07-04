<?php
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistToUser;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddWishlistToUserHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistHasAnotherShopUserException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;

final class AddWishlistToUserHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistCookieTokenResolver
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
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
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
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
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
