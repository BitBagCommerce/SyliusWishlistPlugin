<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlistInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\CopySelectedProductsToOtherWishlistHandler;
use Sylius\WishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicatorInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;

final class CopySelectedProductsToOtherWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductsToOtherWishlistDuplicatorInterface $copyistProductsToWishlist,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $copyistProductsToWishlist,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CopySelectedProductsToOtherWishlistHandler::class);
    }

    public function it_copy_selected_products_to_another_wishlist(
        WishlistInterface $destinedWishlist,
        Collection $wishlistProducts,
        WishlistRepositoryInterface $wishlistRepository,
        CopySelectedProductsToOtherWishlistInterface $copySelectedProductsToOtherWishlist,
        WishlistProductsToOtherWishlistDuplicatorInterface $copyistProductsToWishlist,
    ): void {
        $copySelectedProductsToOtherWishlist->getWishlistProducts()->willReturn($wishlistProducts);
        $copySelectedProductsToOtherWishlist->getDestinedWishlistId()->willReturn(2);

        $wishlistRepository->find(2)->willReturn($destinedWishlist);

        $copyistProductsToWishlist->copyWishlistProductsToOtherWishlist($wishlistProducts, $destinedWishlist)->shouldBeCalled();

        $this->__invoke($copySelectedProductsToOtherWishlist);
    }
}
