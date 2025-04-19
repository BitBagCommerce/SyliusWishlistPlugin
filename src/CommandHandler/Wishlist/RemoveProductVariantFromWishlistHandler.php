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

use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductVariantNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RemoveProductVariantFromWishlistHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private RepositoryInterface $wishlistProductRepository,
        private ObjectManager $wishlistManager,
    ) {
    }

    public function __invoke(RemoveProductVariantFromWishlist $removeProductVariantFromWishlist): WishlistInterface
    {
        $variantId = $removeProductVariantFromWishlist->getProductVariantIdValue();
        $token = $removeProductVariantFromWishlist->getWishlistTokenValue();

        /** @var ?ProductVariantInterface $variant */
        $variant = $this->productVariantRepository->find($variantId);
        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductRepository->findOneBy(['variant' => $variant]);
        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (null === $variant || null === $wishlistProduct) {
            throw new ProductVariantNotFoundException(
                sprintf('The Product %s does not exist', $variantId),
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token),
            );
        }

        $wishlist->removeProductVariant($variant);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
