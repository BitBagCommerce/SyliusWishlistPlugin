<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private ObjectManager $wishlistManager;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(RemoveWishlist $removeWishlist): void
    {
        $token = $removeWishlist->getWishlistTokenValue();
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token)
            );
        }

        $this->wishlistManager->remove($wishlist);
        $this->wishlistManager->flush();
    }
}
