<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddProductToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private WishlistUpdaterInterface $wishlistUpdater;

    private ProductRepositoryInterface $productRepository;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistUpdaterInterface $wishlistUpdater,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistUpdater = $wishlistUpdater;
        $this->productRepository = $productRepository;
    }

    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
    {
        $product = $this->productRepository->find($addProductToWishlist->productId);
        $wishlist = $addProductToWishlist->getWishlist();

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf("The Product %s does not exist", $addProductToWishlist->productId)
            );
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        return $this->wishlistUpdater->addProductToWishlist($wishlist, $wishlistProduct);
    }
}
