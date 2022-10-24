<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Security;

final class WishlistsResolver implements WishlistsResolverInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private Security $security;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private ChannelContextInterface $channelContext;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        Security $security,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->security = $security;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->channelContext = $channelContext;
    }

    public function resolve(): array
    {
        $user = $this->security->getUser();
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $foundException) {
            $channel = null;
        }

        if ($user instanceof ShopUserInterface) {
            return $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
        }

        if ($channel instanceof ChannelInterface) {
            return $this->wishlistRepository->findAllByAnonymousAndChannel($wishlistCookieToken, $channel);
        }

        return $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
    }
}
