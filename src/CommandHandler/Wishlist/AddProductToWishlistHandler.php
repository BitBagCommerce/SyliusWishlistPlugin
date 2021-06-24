<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddProductToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ObjectManager $wishlistManager;

    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
    {
        $product = $this->productRepository->find($addProductToWishlist->product);
        $wishlist = $this->wishlistRepository->findByToken($addProductToWishlist->getWishlistTokenValue());

        if (!$product && !$wishlist) {
            throw new NotFoundHttpException();
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
