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

use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\RemoveProductFromWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class RemoveProductFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager,
    ): void {
        $this->beConstructedWith(
            $productRepository,
            $wishlistRepository,
            $wishlistProductRepository,
            $wishlistManager,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductFromWishlistHandler::class);
    }

    public function it_removes_product_from_wishlist(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager,
        ProductInterface $product,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $removeProductCommand = new RemoveProductFromWishlist(1, 'wishlist_token');

        $productRepository->find(1)->willReturn($product);
        $wishlistRepository->findByToken('wishlist_token')->willReturn($wishlist);
        $wishlistProductRepository
            ->findOneBy(['product' => $product, 'wishlist' => $wishlist])
            ->willReturn($wishlistProduct);

        $wishlist->removeProduct($wishlistProduct)->willReturn($wishlist);
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeProductCommand)->shouldReturn($wishlist);
    }

    public function it_throws_exception_when_product_not_found(
        ProductRepositoryInterface $productRepository,
    ): void {
        $removeProductCommand = new RemoveProductFromWishlist(1, 'wishlist_token');

        $productRepository->find(1)->willReturn(null);

        $this->shouldThrow(ProductNotFoundException::class)->during('__invoke', [$removeProductCommand]);
    }

    public function it_throws_exception_when_wishlist_not_found(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        ProductInterface $product,
        WishlistProductInterface $wishlistProduct,
        RepositoryInterface $wishlistProductRepository,
    ): void {
        $removeProductCommand = new RemoveProductFromWishlist(1, 'wishlist_token');

        $productRepository->find(1)->willReturn($product);
        $wishlistProductRepository->findOneBy([
            'product' => $product,
            'wishlist' => null,
        ])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('wishlist_token')->willReturn(null);

        $this->shouldThrow(WishlistNotFoundException::class)->during('__invoke', [$removeProductCommand]);
    }
}
