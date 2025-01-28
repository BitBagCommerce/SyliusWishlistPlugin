<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RemoveProductFromWishlistHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private WishlistRepositoryInterface $wishlistRepository,
        private RepositoryInterface $wishlistProductRepository,
        private ObjectManager $wishlistManager,
    ) {
    }

    public function __invoke(RemoveProductFromWishlist $removeProductFromWishlist): WishlistInterface
    {
        $productId = $removeProductFromWishlist->getProductIdValue();
        $token = $removeProductFromWishlist->getWishlistTokenValue();

        /** @var ?ProductInterface $product */
        $product = $this->productRepository->find($productId);

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($token);

        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductRepository->findOneBy(['product' => $product, 'wishlist' => $wishlist]);

        if (null === $product || null === $wishlistProduct) {
            throw new ProductNotFoundException(
                sprintf('The Product %s does not exist', $productId),
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token),
            );
        }

        $wishlist = $wishlist->removeProduct($wishlistProduct);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
