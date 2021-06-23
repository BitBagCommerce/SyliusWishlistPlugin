<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductFromWishlistHandler implements MessageHandlerInterface
{
    private ProductRepositoryInterface $productRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository)
    {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(RemoveProductFromWishlist $removeProductFromWishlist): WishlistInterface
    {

    }
}
