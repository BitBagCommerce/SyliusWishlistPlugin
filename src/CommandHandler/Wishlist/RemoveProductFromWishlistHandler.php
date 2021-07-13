<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
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
        $productId = $removeProductFromWishlist->getProductIdValue();
        $token = $removeProductFromWishlist->getWishlistTokenValue();

        $product = $this->productRepository->find($productId);
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf("The Product %s does not exist", $productId)
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf("The Wishlist %s does not exist", $token)
            );
        }

        $this->wishlistUpdater->removeProductFromWishlist($wishlist, $product);
    }
}
