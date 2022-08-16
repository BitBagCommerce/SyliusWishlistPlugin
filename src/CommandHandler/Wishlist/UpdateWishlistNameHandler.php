<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

final class UpdateWishlistNameHandler
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private ChannelContextInterface $channelContext;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->channelContext = $channelContext;
    }

    public function __invoke(UpdateWishlistName $updateWishlistName): void
    {
        $wishlist = $updateWishlistName->getWishlist();

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (null !== $updateWishlistName->getChannelCode()) {
            $channel = $this->channelContext->getChannel();
            $wishlist->setChannel($channel);
        }

        if ($this->wishlistRepository->findOneByTokenAndName($wishlistCookieToken, $updateWishlistName->getName())) {
            throw new WishlistNameIsTakenException();
        }

        $wishlist->setName($updateWishlistName->getName());
        $this->wishlistRepository->add($wishlist);
    }
}
