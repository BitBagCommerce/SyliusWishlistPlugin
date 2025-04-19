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

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\RemoveProductVariantFromWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductVariantNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;

final class RemoveProductVariantFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $productVariantRepository,
            $wishlistProductRepository,
            $wishlistManager,
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
        WishlistProductInterface $wishlistProduct,
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
        ProductVariantRepositoryInterface $productVariantRepository,
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
