<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddProductToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private WishlistUpdaterInterface $wishlistUpdater;

    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistUpdaterInterface $wishlistUpdater,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistUpdater = $wishlistUpdater;
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
    {
        $product = $this->productRepository->find($addProductToWishlist->productId);
        $wishlist = $this->wishlistRepository->findByToken($addProductToWishlist->getWishlistTokenValue());

        if (null === $product || null === $wishlist) {
            throw new NotFoundHttpException();
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        return $this->wishlistUpdater->addProductToWishlist($wishlist, $wishlistProduct);
    }
}
