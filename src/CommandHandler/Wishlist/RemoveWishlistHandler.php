<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveWishlistHandler implements MessageHandlerInterface
{
    private ObjectManager $wishlistManager;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        ObjectManager $wishlistManager,
        WishlistRepositoryInterface $wishlistRepository
    )
    {
        $this->wishlistManager = $wishlistManager;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(RemoveWishlist $removeWishlist)
    {
        $wishlist = $this->wishlistRepository->findByToken($removeWishlist->getWishlistTokenValue());

        $this->wishlistManager->remove($wishlist);
        $this->wishlistManager->flush();
    }
}
