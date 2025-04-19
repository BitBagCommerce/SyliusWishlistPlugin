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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\WishlistPlugin\Command\Wishlist\AddProductToWishlist;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\AddProductToWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductNotFoundException;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;

final class AddProductToWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductRepositoryInterface $productRepository,
        ObjectManager $wishlistManager,
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $productRepository,
            $wishlistManager,
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
        ObjectManager $wishlistManager,
    ): void {
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
        ObjectManager $wishlistManager,
    ): void {
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
}
