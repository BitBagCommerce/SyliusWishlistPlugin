<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistTokenValueAwareInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductToWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

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
    }

    public function it_adds_product_to_wishlist(
        ProductInterface $product,
        WishlistInterface $wishlist,
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager
    ): void
    {
        $productRepository->find(1)->willReturn($product);

        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProduct);
        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();

        $wishlistManager->persist($wishlistProduct)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $addProductToWishlist = new AddProductToWishlist(1);
        $addProductToWishlist->setWishlist($wishlist->getWrappedObject());

        $this->__invoke($addProductToWishlist);
    }

    public function it_doesnt_add_product_to_wishlist_if_product_isnt_found(
        ProductInterface $product,
        WishlistInterface $wishlist,
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager
    ): void
    {
        $productRepository->find(1)->willReturn(null);

        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->shouldNotBeCalled();
        $wishlist->addWishlistProduct($wishlistProduct)->shouldNotBeCalled();

        $wishlistManager->persist($wishlistProduct)->shouldNotBeCalled();
        $wishlistManager->flush()->shouldNotBeCalled();

        $addProductToWishlist = new AddProductToWishlist(1);
        $addProductToWishlist->setWishlist($wishlist->getWrappedObject());

        $this
            ->shouldThrow(ProductNotFoundException::class)
            ->during('__invoke', [$addProductToWishlist])
        ;
    }

//    public function it_doesnt_add_product_to_wishlist_if_wishlist_isnt_found(
//        ProductInterface $product,
//        WishlistInterface $wishlist,
//        ProductRepositoryInterface $productRepository,
//        WishlistProductFactoryInterface $wishlistProductFactory,
//        WishlistProductInterface $wishlistProduct,
//        ObjectManager $wishlistManager
//    ): void
//    {
//        $productRepository->find(1)->willReturn($product);
//
//        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->shouldNotBeCalled();
//        $wishlist->addWishlistProduct($wishlistProduct)->shouldNotBeCalled();
//
//        $wishlistManager->persist($wishlistProduct)->shouldNotBeCalled();
//        $wishlistManager->flush()->shouldNotBeCalled();
//
//        $addProductToWishlist = new AddProductToWishlist(1);
//        $addProductToWishlist->setWishlist($wishlist->getWrappedObject());
//
//        $this
//            ->shouldThrow(WishlistNotFoundException::class)
//            ->during('__invoke', [$addProductToWishlist])
//        ;
//    }
}
