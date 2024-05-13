<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveProductVariantFromWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class RemoveProductVariantFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $productVariantRepository,
            $wishlistProductRepository,
            $wishlistManager
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductVariantFromWishlistHandler::class);
    }

    public function it_removes_product_variant_from_wishlist(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager,
        ProductVariantInterface $variant,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct
    ): void {
        $removeProductVariantCommand = new RemoveProductVariantFromWishlist(1, 'wishlist_token');

        $productVariantRepository->find(1)->willReturn($variant);
        $wishlistProductRepository->findOneBy(['variant' => $variant])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('wishlist_token')->willReturn($wishlist);

        $wishlist->removeProductVariant($variant)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeProductVariantCommand)->shouldReturn($wishlist);
    }

    public function it_throws_exception_when_product_variant_not_found(
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $removeProductVariantCommand = new RemoveProductVariantFromWishlist(1, 'wishlist_token');

        $productVariantRepository->find(1)->willReturn(null);

        $this->shouldThrow(ProductVariantNotFoundException::class)->during('__invoke', [$removeProductVariantCommand]);
    }

    public function it_throws_exception_when_wishlist_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        ProductVariantInterface $variant,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $removeProductVariantCommand = new RemoveProductVariantFromWishlist(1, 'wishlist_token');

        $productVariantRepository->find(1)->willReturn($variant);
        $wishlistProductRepository->findOneBy(['variant' => $variant])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('wishlist_token')->willReturn(null);

        $this->shouldThrow(WishlistNotFoundException::class)->during('__invoke', [$removeProductVariantCommand]);
    }
}
