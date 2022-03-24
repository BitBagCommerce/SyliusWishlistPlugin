<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductToSelectedWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository
    ) {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(AddProductToSelectedWishlist $addProductToSelectedWishlist)
    {
        $product = $addProductToSelectedWishlist->getProduct();
        $wishlist = $addProductToSelectedWishlist->getWishlist();

        if (null === $product) {
            throw new NotFoundHttpException();
        }

        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);
    }
}
