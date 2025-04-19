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

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\CreateWishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use Sylius\WishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class CreateWishlistHandler
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
