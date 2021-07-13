<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistUpdaterInterface $wishlistUpdater;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistUpdaterInterface $wishlistUpdater
    )
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistUpdater = $wishlistUpdater;
    }

    public function __invoke(RemoveWishlist $removeWishlist)
    {
        $wishlist = $this->wishlistRepository->findByToken($removeWishlist->getWishlistTokenValue());

        $this->wishlistUpdater->removeWishlist($wishlist);
    }
}
