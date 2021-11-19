<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductVariantFromWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private RepositoryInterface $wishlistProductRepository;

    private ObjectManager $wishlistManager;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistProductRepository = $wishlistProductRepository;
        $this->wishlistManager = $wishlistManager;
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
                sprintf('The Product %s does not exist', $variantId)
            );
        }

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                sprintf('The Wishlist %s does not exist', $token)
            );
        }

        $wishlist->removeProductVariant($variant);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
