<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductFromWishlistHandler implements MessageHandlerInterface
{
    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistUpdaterInterface $wishlistUpdater;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistUpdaterInterface $wishlistUpdater
    )
    {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistUpdater = $wishlistUpdater;
    }

    public function __invoke(RemoveProductFromWishlist $removeProductFromWishlist)
    {
        $product = $this->productRepository->find($removeProductFromWishlist->getProductIdValue());
        $wishlist = $this->wishlistRepository->findByToken($removeProductFromWishlist->getWishlistTokenValue());

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf("The Product %s does not exist", $removeProductFromWishlist->getProductIdValue())
            );
        }

        if (null === $wishlist) {
            throw new NotFoundHttpException();
        }

        $this->wishlistUpdater->removeProductFromWishlist($wishlist, $product);
    }
}
