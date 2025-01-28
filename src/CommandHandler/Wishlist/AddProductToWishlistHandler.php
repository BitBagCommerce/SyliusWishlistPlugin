<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddProductToWishlistHandler
{
    public function __construct(
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private ProductRepositoryInterface $productRepository,
        private ObjectManager $wishlistManager,
    ) {
    }

    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
    {
        $productId = $addProductToWishlist->productId;

        /** @var ?ProductInterface $product */
        $product = $this->productRepository->find($productId);

        $wishlist = $addProductToWishlist->getWishlist();

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf('The Product %s does not exist', $productId),
            );
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlistProduct);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
