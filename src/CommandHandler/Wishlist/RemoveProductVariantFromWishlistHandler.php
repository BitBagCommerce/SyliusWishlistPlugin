<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RemoveProductVariantFromWishlistHandler
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
