<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ProductRepositoryInterface $productRepository;

    private ObjectManager $wishlistManager;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductRepositoryInterface $productRepository,
        ObjectManager $wishlistManager
    ) {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->productRepository = $productRepository;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
    {
        $productId = $addProductToWishlist->productId;

        /** @var ?ProductInterface $product */
        $product = $this->productRepository->find($productId);
        $wishlist = $addProductToWishlist->getWishlist();

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf('The Product %s does not exist', $productId)
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException();
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();


        return $wishlist;
    }
}
