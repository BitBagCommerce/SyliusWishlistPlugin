<?php
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveProductFromWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class RemoveProductFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager
    ): void {
        $this->beConstructedWith(
            $productRepository,
            $wishlistRepository,
            $wishlistProductRepository,
            $wishlistManager
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
        WishlistProductInterface $wishlistProduct
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
        ProductInterface $product
    ): void {
        $removeProductCommand = new RemoveProductFromWishlist(1, 'wishlist_token');

        $productRepository->find(1)->willReturn($product);
        $wishlistRepository->findByToken('wishlist_token')->willReturn(null);

        $this->shouldThrow(WishlistNotFoundException::class)->during('__invoke', [$removeProductCommand]);
    }
}
