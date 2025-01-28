<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RemoveWishlistHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private ObjectManager $wishlistManager,
    ) {
    }

    public function __invoke(RemoveWishlist $removeWishlist): void
    {
        $token = $removeWishlist->getWishlistTokenValue();
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token),
            );
        }

        $this->wishlistManager->remove($wishlist);
        $this->wishlistManager->flush();
    }
}
