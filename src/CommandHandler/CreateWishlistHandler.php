<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler;

use BitBag\SyliusWishlistPlugin\Command\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class CreateWishlistHandler
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private WishlistFactoryInterface $wishlistFactory,
        private ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        private ObjectManager $wishlistManager,
        private ChannelRepositoryInterface $channelRepository,
        private TokenUserResolverInterface $tokenUserResolver,
        private RequestStack $requestStack,
        private string $wishlistCookieToken,
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

        if (null !== $createWishlist->getTokenValue()) {
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
