<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\CreateNewWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class CreateNewWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelRepositoryInterface $channelRepository
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $tokenStorage,
            $wishlistFactory,
            $wishlistCookieTokenResolver,
            $channelRepository
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateNewWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_creates_wishlist_for_user(
        TokenInterface $token,
        TokenStorageInterface $tokenStorage,
        ShopUserInterface $user,
        WishlistInterface $wishlist,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        CreateNewWishlist $createNewWishlist,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        WishlistRepositoryInterface $wishlistRepository
    ): void
    {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $wishlistFactory->createForUser($user)->willReturn($wishlist);
        $wishlistCookieTokenResolver->resolve()->willReturn("cookieToken");
        $channelRepository->findOneByCode("createNewWishlist")->willReturn($channel);
        $createNewWishlist->getChannelCode()->willReturn("createNewWishlist");
        $createNewWishlist->getName()->willReturn("wishlistName");

        $wishlist->setToken("cookieToken")->shouldBeCalledOnce();
        $wishlist->setChannel($channel)->shouldBeCalledOnce();
        $wishlist->setName("wishlistName")->shouldBeCalledOnce();
        $wishlistRepository->add($wishlist)->shouldBeCalledOnce();

        $this->__invoke($createNewWishlist);

    }
}
