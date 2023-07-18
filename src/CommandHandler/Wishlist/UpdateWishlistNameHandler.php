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

final class UpdateWishlistNameHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
    ) {}

    public function __invoke(UpdateWishlistName $updateWishlistName): void
    {
        $wishlist = $updateWishlistName->getWishlist();

        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if ($this->wishlistRepository->findOneByTokenAndName($wishlistCookieToken, $updateWishlistName->getName())) {
            throw new WishlistNameIsTakenException();
        }

        $wishlist->setName($updateWishlistName->getName());
        $this->wishlistRepository->add($wishlist);
    }
}
