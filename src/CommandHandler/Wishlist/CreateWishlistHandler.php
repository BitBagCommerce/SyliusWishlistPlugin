<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class CreateWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private ShopUserWishlistResolverInterface $shopUserWishlistResolver;

    private ObjectManager $wishlistManager;

    private ChannelRepositoryInterface $channelRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        ObjectManager $wishlistManager,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->shopUserWishlistResolver = $shopUserWishlistResolver;
        $this->wishlistManager = $wishlistManager;
        $this->channelRepository = $channelRepository;
    }

    public function __invoke(CreateWishlist $createWishlist): WishlistInterface
    {
        /** @var ?TokenInterface $token */
        $token = $this->tokenStorage->getToken();

        $user = null !== $token ? $token->getUser() : null;

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->shopUserWishlistResolver->resolve($user);
        }

        $channelCode = $createWishlist->getChannelCode();
        $channel = null !== $channelCode ? $this->channelRepository->findOneByCode($channelCode) : null;

        if (null !== $channel) {
            $wishlist->setChannel($channel);
        }

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
