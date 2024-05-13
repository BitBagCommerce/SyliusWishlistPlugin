<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\CreateNewWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CreateNewWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelRepositoryInterface $channelRepository,
        WishlistNameCheckerInterface $wishlistNameChecker,
        TokenUserResolverInterface $tokenUserResolver
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $tokenStorage,
            $wishlistFactory,
            $wishlistCookieTokenResolver,
            $channelRepository,
            $wishlistNameChecker,
            $tokenUserResolver
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateNewWishlistHandler::class);
    }

    public function it_creates_new_wishlist_for_user(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        WishlistNameCheckerInterface $wishlistNameChecker,
        ChannelRepositoryInterface $channelRepository,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        WishlistInterface $wishlist,
        WishlistInterface $existingWishlist,
        ChannelInterface $channel,
        TokenUserResolverInterface $tokenUserResolver
    ): void {
        $wishlists = [$existingWishlist];

        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn($shopUser);

        $wishlistCookieTokenResolver->resolve()->willReturn('token');
        $wishlistFactory->createForUser($shopUser)->willReturn($wishlist);

        $wishlist->getShopUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn(1);
        $wishlistRepository->findAllByShopUser(1)->willReturn($wishlists);

        $wishlist->setToken('token')->shouldBeCalled();
        $channelRepository->findOneByCode('test_channel_code')->willReturn($channel);
        $wishlist->setChannel($channel)->shouldBeCalled();

        $existingWishlist->getName()->willReturn('existing');
        $wishlistNameChecker->check('existing', 'New wishlist')->willReturn(false);
        $wishlist->setName('New wishlist');

        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $createNewWishlist = new CreateNewWishlist('New wishlist', 'test_channel_code');

        $this->__invoke($createNewWishlist);
    }

    public function it_creates_new_wishlist_for_guest(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelRepositoryInterface $channelRepository,
        WishlistInterface $newWishlist,
        ChannelInterface $channel,
        TokenUserResolverInterface $tokenUserResolver
    ): void {
        $tokenStorage->getToken()->willReturn(null);
        $tokenUserResolver->resolve(null)->willReturn(null);

        $wishlistCookieTokenResolver->resolve()->willReturn('token');
        $wishlistFactory->createNew()->willReturn($newWishlist);

        $wishlistRepository->findAllByAnonymous('token')->willReturn([]);

        $newWishlist->setToken('token')->shouldBeCalled();
        $channelRepository->findOneByCode('test_channel_code')->willReturn($channel);
        $newWishlist->setChannel($channel)->shouldBeCalled();

        $wishlistRepository->add($newWishlist)->shouldBeCalled();

        $createNewWishlist = new CreateNewWishlist('New wishlist', 'test_channel_code');

        $this->__invoke($createNewWishlist);
    }

    public function it_doesnt_add_duplicated_wishlist_name_for_user(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        WishlistNameCheckerInterface $wishlistNameChecker,
        ChannelRepositoryInterface $channelRepository,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        WishlistInterface $wishlist,
        WishlistInterface $existingWishlist,
        ChannelInterface $channel,
        TokenUserResolverInterface $tokenUserResolver
    ): void {
        $wishlists = [$existingWishlist];

        $tokenStorage->getToken()->willReturn($token);
        $tokenUserResolver->resolve($token)->willReturn($shopUser);

        $wishlistCookieTokenResolver->resolve()->willReturn('token');
        $wishlistFactory->createForUser($shopUser)->willReturn($wishlist);

        $wishlist->getShopUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn(1);
        $wishlistRepository->findAllByShopUser(1)->willReturn($wishlists);

        $wishlist->setToken('token')->shouldBeCalled();
        $channelRepository->findOneByCode('test_channel_code')->willReturn($channel);
        $wishlist->setChannel($channel)->shouldBeCalled();

        $existingWishlist->getName()->willReturn('existing');
        $wishlistNameChecker->check('existing', 'existing')->willReturn(true);
        $wishlist->setName('existing')->shouldNotBeCalled();

        $wishlistRepository->add($wishlist)->shouldNotBeCalled();

        $createNewWishlist = new CreateNewWishlist('existing', 'test_channel_code');

        $this
            ->shouldThrow(WishlistNameIsTakenException::class)
            ->during('__invoke', [$createNewWishlist])
        ;
    }
}
