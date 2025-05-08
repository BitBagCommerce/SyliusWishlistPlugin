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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\WishlistPlugin\Command\Wishlist\AddProductToSelectedWishlistInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\AddProductToSelectedWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;

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
