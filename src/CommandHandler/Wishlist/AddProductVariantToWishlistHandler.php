<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductVariantToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddProductVariantToWishlistHandler
{
    public function __construct(
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ObjectManager $wishlistManager,
    ) {
    }

    public function __invoke(AddProductVariantToWishlist $addProductVariantToWishlist): WishlistInterface
    {
        $variantId = $addProductVariantToWishlist->productVariantId;

        /** @var ?ProductVariantInterface $variant */
        $variant = $this->productVariantRepository->find($variantId);
        $wishlist = $addProductVariantToWishlist->getWishlist();

        if (null === $variant) {
            throw new ProductVariantNotFoundException(
                sprintf('The ProductVariant %s does not exist', $variantId),
            );
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $variant);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
