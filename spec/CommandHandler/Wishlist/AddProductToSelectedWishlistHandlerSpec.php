<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\AddProductToSelectedWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\AddProductToSelectedWishlistHandler;
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
        WishlistRepositoryInterface $wishlistRepository,
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $wishlistRepository,
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
        WishlistRepositoryInterface $wishlistRepository,
    ): void {
        $addProductToSelectedWishlist->getProduct()->willReturn($product);
        $addProductToSelectedWishlist->getWishlist()->willReturn($wishlist);

        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $this->__invoke($addProductToSelectedWishlist);
    }
}
