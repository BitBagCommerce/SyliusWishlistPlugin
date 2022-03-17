<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\CopySelectedProductsToOtherWishlistHandler;
use BitBag\SyliusWishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicatorInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;

final class CopySelectedProductsToOtherWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductsToOtherWishlistDuplicatorInterface $copyistProductsToWishlist
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $copyistProductsToWishlist
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
        WishlistProductsToOtherWishlistDuplicatorInterface $copyistProductsToWishlist
    ): void {
        $copySelectedProductsToOtherWishlist->getWishlistProducts()->willReturn($wishlistProducts);
        $copySelectedProductsToOtherWishlist->getDestinedWishlistId()->willReturn(2);

        $wishlistRepository->find(2)->willReturn($destinedWishlist);

        $copyistProductsToWishlist->copyWishlistProductsToOtherWishlist($wishlistProducts, $destinedWishlist)->shouldBeCalled();

        $this->__invoke($copySelectedProductsToOtherWishlist);
    }
}
