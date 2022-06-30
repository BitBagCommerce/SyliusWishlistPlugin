<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductToWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductToWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductRepositoryInterface $productRepository,
        ObjectManager $wishlistManager
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $productRepository,
            $wishlistManager
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductToWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_throws_404_when_product_is_not_found(
        AddProductToWishlistInterface $addProductToWishlist,
        ProductRepositoryInterface $productRepository,
        WishlistInterface $wishlist
    ): void {
        $addProductToWishlist->getProductId()->willReturn(1);
        $productRepository->find(1)->willReturn(null);
        $addProductToWishlist->getWishlist()->willReturn($wishlist);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$addProductToWishlist]);
    }

    public function it_throws_404_when_wishlist_is_not_found(
        AddProductToWishlistInterface $addProductToWishlist,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ): void {
        $addProductToWishlist->getProductId()->willReturn(1);
        $productRepository->find(1)->willReturn($product);
        $addProductToWishlist->getWishlist()->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$addProductToWishlist]);
    }

    public function it_adds_product_to_wishlist(
        AddProductToWishlistInterface $addProductToWishlist,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        WishlistProductFactoryInterface $wishlistProductFactory,
        ObjectManager $wishlistManager
    ): void
    {
        $addProductToWishlist->getProductId()->willReturn(1);
        $productRepository->find(1)->willReturn($product);
        $addProductToWishlist->getWishlist()->willReturn($wishlist);
        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->persist($wishlistProduct)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($addProductToWishlist)->shouldReturn($wishlist);
    }
}
