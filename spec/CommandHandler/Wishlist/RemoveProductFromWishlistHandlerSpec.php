<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveProductFromWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

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
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_throws_404_when_product_and_wishlist_product_not_found(
        RemoveProductFromWishlistInterface $removeProductFromWishlist,
        ProductRepositoryInterface $productRepository,
        RepositoryInterface $wishlistProductRepository,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist
    ): void
    {
        $removeProductFromWishlist->getProductIdValue()->willReturn(1);
        $removeProductFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productRepository->find(1)->willReturn(null);
        $wishlistProductRepository->findOneBy(['product' => null])->willReturn(null);
        $wishlistRepository->findByToken('one')->willReturn($wishlist);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeProductFromWishlist]);
    }

    public function it_throws_404_when_wishlist_not_found(
        RemoveProductFromWishlistInterface $removeProductFromWishlist,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        RepositoryInterface $wishlistProductRepository,
        WishlistProductInterface $wishlistProduct,
        WishlistRepositoryInterface $wishlistRepository
    ): void
    {
        $removeProductFromWishlist->getProductIdValue()->willReturn(1);
        $removeProductFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productRepository->find(1)->willReturn($product);
        $wishlistProductRepository->findOneBy(['product' => $product])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('one')->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeProductFromWishlist]);
    }

    public function it_removes_product_from_wishlist(
        RemoveProductFromWishlistInterface $removeProductFromWishlist,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        RepositoryInterface $wishlistProductRepository,
        WishlistProductInterface $wishlistProduct,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        ObjectManager $wishlistManager
    ): void
    {
        $removeProductFromWishlist->getProductIdValue()->willReturn(1);
        $removeProductFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productRepository->find(1)->willReturn($product);
        $wishlistProductRepository->findOneBy(['product' => $product])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('one')->willReturn($wishlist);
        $wishlist->removeProduct($wishlistProduct)->willReturn($wishlist);

        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeProductFromWishlist)->shouldReturn($wishlist);
    }
}
