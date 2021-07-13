<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductVariantToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductVariantToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private WishlistUpdaterInterface $wishlistUpdater;

    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistUpdaterInterface $wishlistUpdater,
        ProductVariantRepositoryInterface $productVariantRepository
    )
    {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistUpdater = $wishlistUpdater;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function __invoke(AddProductVariantToWishlist $addProductVariantToWishlist): WishlistInterface
    {
        $variant = $this->productVariantRepository->find($addProductVariantToWishlist->productVariantId);
        $wishlist = $addProductVariantToWishlist->getWishlist();

        if (null === $variant) {
            throw new ProductVariantNotFoundException(
                sprintf("The ProductVariant %s does not exist", $addProductVariantToWishlist->productVariantId)
            );
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $variant);

        return $this->wishlistUpdater->addProductToWishlist($wishlist, $wishlistProduct);
    }
}
