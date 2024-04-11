<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveWishlistHandler implements MessageHandlerInterface
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private ObjectManager $wishlistManager
    ) {
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
