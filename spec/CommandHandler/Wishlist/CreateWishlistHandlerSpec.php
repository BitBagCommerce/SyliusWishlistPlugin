<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\CreateWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        ObjectManager $wishlistManager,
        ChannelRepositoryInterface $channelRepository
    ): void {
        $this->beConstructedWith(
            $tokenStorage,
            $wishlistFactory,
            $shopUserWishlistResolver,
            $wishlistManager,
            $channelRepository
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_creates_wishlist(
        TokenInterface $token,
        TokenStorageInterface $tokenStorage,
        UserInterface $user,
        WishlistInterface $wishlist,
        WishlistFactoryInterface $wishlistFactory,
        ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        CreateWishlist $createWishlist,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ObjectManager $wishlistManager
    ): void
    {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $wishlistFactory->createNew()->willReturn($wishlist);
        $shopUserWishlistResolver->resolve($user)->willReturn($wishlist);
        $createWishlist->getChannelCode()->willReturn('channelCode');
        $channelRepository->findOneByCode('channelCode')->willReturn($channel);


//        $user->shouldBeAnInstanceOf(ShopUserInterface::class);
        $wishlist->setChannel($channel)->shouldBeCalledOnce();
        $wishlistManager->persist($wishlist)->shouldBeCalledOnce();
        $wishlistManager->flush()->shouldBeCalledOnce();

        $this->__invoke($createWishlist)->shouldReturn($wishlist);
    }

}

