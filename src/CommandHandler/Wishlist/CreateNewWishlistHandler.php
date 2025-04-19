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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Command\Wishlist\CreateNewWishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\WishlistNameIsTakenException;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class CreateNewWishlistHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private TokenStorageInterface $tokenStorage,
        private WishlistFactoryInterface $wishlistFactory,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private ChannelRepositoryInterface $channelRepository,
        private TokenUserResolverInterface $tokenUserResolver,
    ) {
    }

    public function __invoke(CreateNewWishlist $createNewWishlist): int
    {
        $token = $this->tokenStorage->getToken();
        $user = $this->tokenUserResolver->resolve($token);

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if ($user instanceof ShopUserInterface) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistFactory->createForUser($user);
            /** @var ?ShopUserInterface $wishlistShopUser */
            $wishlistShopUser = $wishlist->getShopUser();
            Assert::notNull($wishlistShopUser);
            $wishlists = $this->wishlistRepository->findAllByShopUser($wishlistShopUser->getId());
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistFactory->createNew();
            $wishlists = $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
        }

        if ('' !== $wishlistCookieToken) {
            $wishlist->setToken($wishlistCookieToken);
        }

        if (null !== $createNewWishlist->getChannelCode()) {
            $channel = $this->channelRepository->findOneByCode($createNewWishlist->getChannelCode());
            $wishlist->setChannel($channel);
        }

        if (0 === count($wishlists)) {
            $wishlist->setName($createNewWishlist->getName());
        } else {
            /** @var WishlistInterface $newWishlist */
            foreach ($wishlists as $newWishlist) {
                if ((string) $newWishlist->getName() !== $createNewWishlist->getName()) {
                    $wishlist->setName($createNewWishlist->getName());
                } else {
                    throw new WishlistNameIsTakenException();
                }
            }
        }

        $this->wishlistRepository->add($wishlist);

        return $wishlist->getId();
    }
}
