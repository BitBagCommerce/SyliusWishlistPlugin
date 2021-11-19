<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductFromWishlistHandler implements MessageHandlerInterface
{
    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    private RepositoryInterface $wishlistProductRepository;

    private ObjectManager $wishlistManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductRepository = $wishlistProductRepository;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(RemoveProductFromWishlist $removeProductFromWishlist): WishlistInterface
    {
        $productId = $removeProductFromWishlist->getProductIdValue();
        $token = $removeProductFromWishlist->getWishlistTokenValue();

        /** @var ?ProductInterface $product */
        $product = $this->productRepository->find($productId);
        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductRepository->findOneBy(['product' => $product]);
        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (null === $product || null === $wishlistProduct) {
            throw new ProductNotFoundException(
                sprintf('The Product %s does not exist', $productId)
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token)
            );
        }

        $wishlist = $wishlist->removeProduct($wishlistProduct);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
