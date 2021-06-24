<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\AnonymousWishlistResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddProductToWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private ShopUserWishlistResolverInterface $shopUserWishlistResolver;

    private AnonymousWishlistResolverInterface $anonymousWishlistResolver;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ObjectManager $wishlistManager;

    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->tokenStorage = $tokenStorage;
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
