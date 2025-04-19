<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Twig;

use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class WishlistExtension extends AbstractExtension
{
    private const WISHLIST_USER_TOKEN_CACHE_PATTERN = 'user_id_%s_token_%s';

    private const WISHLIST_USER_CHANNEL_CACHE_PATTERN = 'user_id_%s_channel_%s';

    private const WISHLIST_ANONYMOUS_CHANNEL_CACHE_PATTERN = 'anonymous_channel_id_%s';

    private const WISHLIST_ANONYMOUS_CACHE_KEY = 'anonymous_token_%s';

    private const WISHLIST_USER_CACHE_KEY = 'user_id_%s';

    private const WISHLIST_ALL_CACHE_KEY = 'all';

    private array $wishlists = [];

    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getWishlists', [$this, 'getWishlists']),
            new TwigFunction('findAllByShopUser', [$this, 'findAllByShopUser']),
            new TwigFunction('findAllByAnonymous', [$this, 'findAllByAnonymous']),
            new TwigFunction('findAllByShopUserAndToken', [$this, 'findAllByShopUserAndToken']),
            new TwigFunction('findAllByShopUserAndChannel', [$this, 'findAllByShopUserAndChannel']),
            new TwigFunction('findAllByAnonymousAndChannel', [$this, 'findAllByAnonymousAndChannel']),
        ];
    }

    public function getWishlists(): ?array
    {
        if (false === isset($this->wishlists[self::WISHLIST_ALL_CACHE_KEY])) {
            $this->wishlists[self::WISHLIST_ALL_CACHE_KEY] = $this->wishlistRepository->findAll();
        }

        return $this->wishlists[self::WISHLIST_ALL_CACHE_KEY];
    }

    public function findAllByShopUser(UserInterface $user = null): ?array
    {
        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }

        $cacheKey = sprintf(self::WISHLIST_USER_CACHE_KEY, $user->getId());
        if (false === isset($this->wishlists[$cacheKey])) {
            $this->wishlists[$cacheKey] = $this->wishlistRepository->findAllByShopUser($user->getId());
        }

        return $this->wishlists[$cacheKey];
    }

    public function findAllByAnonymous(): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        $cacheKey = sprintf(self::WISHLIST_ANONYMOUS_CACHE_KEY, $wishlistCookieToken);

        if (false === isset($this->wishlists[$cacheKey])) {
            $this->wishlists[$cacheKey] = $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
        }

        return $this->wishlists[$cacheKey];
    }

    public function findAllByShopUserAndToken(UserInterface $user = null): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }

        $cacheKey = sprintf(self::WISHLIST_USER_TOKEN_CACHE_PATTERN, $user->getId(), $wishlistCookieToken);
        if (false === isset($this->wishlists[$cacheKey])) {
            $this->wishlists[$cacheKey] = $this->wishlistRepository->findAllByShopUserAndToken($user->getId(), $wishlistCookieToken);
        }

        return $this->wishlists[$cacheKey];
    }

    public function findAllByShopUserAndChannel(UserInterface $user = null, ChannelInterface $channel = null): ?array
    {
        if (!$user instanceof ShopUserInterface) {
            throw new UnsupportedUserException();
        }
        if (!$channel instanceof ChannelInterface) {
            throw new ChannelNotFoundException();
        }

        $cacheKey = sprintf(self::WISHLIST_USER_CHANNEL_CACHE_PATTERN, $user->getId(), $channel->getCode());
        if (false === isset($this->wishlists[$cacheKey])) {
            $this->wishlists[$cacheKey] = $this->wishlistRepository->findAllByShopUserAndChannel($user, $channel);
        }

        return $this->wishlists[$cacheKey];
    }

    public function findAllByAnonymousAndChannel(ChannelInterface $channel): ?array
    {
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        $cacheKey = sprintf(self::WISHLIST_ANONYMOUS_CHANNEL_CACHE_PATTERN, $channel->getCode());

        if (false === isset($this->wishlists[$cacheKey])) {
            $this->wishlists[$cacheKey] = $this->wishlistRepository->findAllByAnonymousAndChannel($wishlistCookieToken, $channel);
        }

        return $this->wishlists[$cacheKey];
    }
}
