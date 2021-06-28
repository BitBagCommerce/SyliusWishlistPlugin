<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductVariantFromWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private ObjectManager $wishlistProductManager;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        ObjectManager $wishlistProductManager
    )
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductManager = $wishlistProductManager;
    }

    public function __invoke(RemoveProductVariantFromWishlist $removeProductVariantFromWishlist)
    {
        $variant = $this->productVariantRepository->find($removeProductVariantFromWishlist->getVariantId());
        $wishlist = $this->wishlistRepository->findByToken($removeProductVariantFromWishlist->getWishlistToken());

        if (!$variant || !$wishlist) {
            throw new NotFoundHttpException();
        }

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($variant === $wishlistProduct->getVariant()) {
                $this->wishlistProductManager->remove($wishlistProduct);
            }
        }

        $this->wishlistProductManager->flush();
    }
}
