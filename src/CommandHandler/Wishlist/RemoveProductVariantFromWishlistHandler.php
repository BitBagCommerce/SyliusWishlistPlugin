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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\ResourceActions;
use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductVariantNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsMessageHandler]
final class RemoveProductVariantFromWishlistHandler
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private RepositoryInterface $wishlistProductRepository,
        private ObjectManager $wishlistManager,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function __invoke(RemoveProductVariantFromWishlist $removeProductVariantFromWishlist): WishlistInterface
    {
        $variantId = $removeProductVariantFromWishlist->getProductVariantIdValue();
        $token = $removeProductVariantFromWishlist->getWishlistTokenValue();

        /** @var ?ProductVariantInterface $variant */
        $variant = $this->productVariantRepository->find($variantId);

        if (null === $variant) {
            throw new ProductVariantNotFoundException(
                sprintf('The Product Variant %s does not exist', $variantId),
            );
        }

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($token);

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token),
            );
        }

        if (!$this->authorizationChecker->isGranted(ResourceActions::DELETE, $wishlist)) {
            throw new AccessDeniedException('You are not allowed to delete from this wishlist.');
        }

        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductRepository->findOneBy(['variant' => $variant, 'wishlist' => $wishlist]);

        if (null === $wishlistProduct) {
            throw new ProductVariantNotFoundException(
                sprintf('The Product Variant %s was not found in Wishlist %s', $variantId, $token),
            );
        }

        $wishlist->removeProduct($wishlistProduct);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
