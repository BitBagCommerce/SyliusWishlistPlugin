<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductToSelectedWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;

final class AddProductToSelectedWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $wishlistRepository
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductToSelectedWishlistHandler::class);
    }

    public function it_adds_product_to_wishlist_if_product_is_found(
        AddProductToSelectedWishlistInterface $addProductToSelectedWishlist,
        ProductInterface $product,
        WishlistInterface $wishlist,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        WishlistRepositoryInterface $wishlistRepository
    ): void
    {
        $addProductToSelectedWishlist->getProduct()->willReturn($product);
        $addProductToSelectedWishlist->getWishlist()->willReturn($wishlist);

        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $this->__invoke($addProductToSelectedWishlist);
    }
}
