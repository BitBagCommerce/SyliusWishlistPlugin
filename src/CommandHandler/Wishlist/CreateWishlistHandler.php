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
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Webmozart\Assert\Assert;

final class CreateWishlistHandler implements MessageHandlerInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private WishlistFactoryInterface $wishlistFactory,
        private ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        private ObjectManager $wishlistManager,
        private ChannelRepositoryInterface $channelRepository,
        private TokenUserResolverInterface $tokenUserResolver,
        private RequestStack $requestStack,
        private string $wishlistCookieToken
    ) {
    }

    public function __invoke(CreateWishlist $createWishlist): WishlistInterface
    {
        /** @var ?TokenInterface $token */
        $token = $this->tokenStorage->getToken();
        $user = $this->tokenUserResolver->resolve($token);

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();
        $wishlist->setName('Wishlist');

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->shopUserWishlistResolver->resolve($user);
        }

        if (null !== $createWishlist->getTokenValue())
        {
            $wishlist->setToken($createWishlist->getTokenValue());
            $mainRequest = $this->requestStack->getMainRequest();

            Assert::notNull($mainRequest, 'The handler is destined to HTTP context only');
            $mainRequest->attributes->set($this->wishlistCookieToken, $createWishlist->getTokenValue());
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
