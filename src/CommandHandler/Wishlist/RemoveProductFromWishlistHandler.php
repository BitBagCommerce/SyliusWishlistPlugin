<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductFromWishlistHandler implements MessageHandlerInterface
{
    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    private ObjectManager $wishlistProductManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistProductManager
    )
    {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductManager = $wishlistProductManager;
    }

    public function __invoke(RemoveProductFromWishlist $removeProductFromWishlist)
    {
        $product = $this->productRepository->find($removeProductFromWishlist->getProductId());
        $wishlist = $this->wishlistRepository->findByToken($removeProductFromWishlist->getWishlistToken());

        if (null === $product || null === $wishlist) {
            throw new NotFoundHttpException();
        }

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($product === $wishlistProduct->getProduct()) {
                $this->wishlistProductManager->remove($wishlistProduct);
            }
        }

        $this->wishlistProductManager->flush();
    }
}
