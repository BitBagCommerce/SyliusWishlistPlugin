<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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
        private WishlistNameCheckerInterface $wishlistNameChecker,
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
            if ($user instanceof ShopUserInterface) {
                $wishlist->setToken($wishlistCookieToken);
            } else {
                $wishlist->setToken($this->wishlistCookieTokenResolver->new());
            }
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
                if (!$this->wishlistNameChecker->check((string) $newWishlist->getName(), $createNewWishlist->getName())) {
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
