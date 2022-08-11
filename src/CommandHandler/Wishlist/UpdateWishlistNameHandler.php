<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class UpdateWishlistNameHandler
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private ChannelRepositoryInterface $channelRepository;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->channelRepository = $channelRepository;
    }

    public function __invoke(UpdateWishlistName $updateWishlistName): void
    {
        $wishlist = $updateWishlistName->getWishlist();

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (null !== $updateWishlistName->getChannelCode()) {
            $channel = $this->channelRepository->findOneByCode($updateWishlistName->getChannelCode());
            $wishlist->setChannel($channel);
        }

        $wishlists = $this->wishlistRepository->findAllByToken($wishlistCookieToken);

        /** @var WishlistInterface $wishlist */
        foreach ($wishlists as $existingWishlist) {
            if ($existingWishlist->getName() !== $updateWishlistName->getName()) {
                continue;
            } else {
                throw new WishlistNameIsTakenException();
            }
        }

        $wishlist->setName($updateWishlistName->getName());
        $this->wishlistRepository->add($wishlist);
    }
}
