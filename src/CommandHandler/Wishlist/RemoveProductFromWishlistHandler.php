<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RemoveProductFromWishlistHandler
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
