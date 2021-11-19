<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductVariantToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private ObjectManager $wishlistManager;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        ObjectManager $wishlistManager
    ) {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(AddProductVariantToWishlist $addProductVariantToWishlist): WishlistInterface
    {
        $variantId = $addProductVariantToWishlist->productVariantId;

        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantRepository->find($variantId);
        $wishlist = $addProductVariantToWishlist->getWishlist();

        if (null == $variant) {
            throw new ProductVariantNotFoundException(
                sprintf('The ProductVariant %s does not exist', $variantId)
            );
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $variant);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
