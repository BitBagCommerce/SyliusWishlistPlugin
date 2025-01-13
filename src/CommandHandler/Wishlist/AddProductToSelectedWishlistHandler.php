<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\ProductFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;


#[AsMessageHandler]
final class AddProductToSelectedWishlistHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private RepositoryInterface $wishlistProductRepository,
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private WishlistRepositoryInterface $wishlistRepository,
    ) {
    }

    public function __invoke(AddProductToSelectedWishlistInterface $addProductToSelectedWishlist): void
    {
        $productId = $addProductToSelectedWishlist->getProductId();
        $token = $addProductToSelectedWishlist->getWishlistToken();

        /** @var ?ProductInterface $product */
        $product = $this->productRepository->find($productId);

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($token);

        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductRepository->findOneBy(['product' => $product, 'wishlist' => $wishlist]);

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf('The Product %s does not exist', $productId),
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token),
            );
        }

        if (null !== $wishlistProduct) {
            throw new ProductFoundException(
                sprintf('The Product %s already exists in wishlist', $productId)
            );
        }

        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);
    }
}
