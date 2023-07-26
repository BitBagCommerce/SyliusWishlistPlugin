<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WishlistsResolver implements WishlistsResolverInterface
{
    use HandleTrait;

    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorageInterface $tokenStorage;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private ChannelContextInterface $channelContext;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext,
        MessageBusInterface $messageBus
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->channelContext = $channelContext;
        $this->messageBus = $messageBus;
    }

    public function resolve(): array
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $foundException) {
            $channel = null;
        }

        $wishlists = $this->getWishlistsByUserOrCookieToken($wishlistCookieToken, $user, $channel);

        if (null !== $user) {
            $this->checkAndUpdateMissingShopUserRelations($wishlists, $user);
        }

        return $wishlists;
    }

    public function resolveAndCreate(): array
    {
        $wishlists = $this->resolve();

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $foundException) {
            $channel = null;
        }

        if ([] === $wishlists || null === $wishlists)
        {
            $createWishlist = new CreateWishlist($wishlistCookieToken, $channel?->getCode());
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->handle($createWishlist);

            $wishlists = [$wishlist];
        }

        return $wishlists;
    }

    public function getWishlistsByUserOrCookieToken(
        string $wishlistCookieToken,
        ?UserInterface $user,
        ?ChannelInterface $channel
    ): ?array {
        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
        }

        if ($channel instanceof ChannelInterface) {
            return $this->wishlistRepository->findAllByAnonymousAndChannel($wishlistCookieToken, $channel);
        }

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }

    private function checkAndUpdateMissingShopUserRelations(array $wishlists, ShopUserInterface $shopUser)
    {
        /** @var WishlistInterface $wishlist */
        foreach ($wishlists as $wishlist)
        {
            if (null !== $wishlist->getShopUser())
            {
                continue;
            }

            $wishlist->setShopUser($shopUser);
            $this->wishlistRepository->add($wishlist);
        }
    }
}
