<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Remover;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;

final class AnonymousWishlistsRemover implements AnonymousWishlistsRemoverInterface
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private ObjectManager $wishlistManager,
        private ?string $expirationPeriod,
    ) {
    }

    public function remove(): void
    {
        $this->wishlistRepository->deleteAllAnonymousUntil(
            new \DateTime('-' . $this->expirationPeriod),
        );
    }
}
