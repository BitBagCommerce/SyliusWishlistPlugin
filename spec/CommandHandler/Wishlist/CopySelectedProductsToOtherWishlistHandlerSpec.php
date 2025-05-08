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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlistInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\CopySelectedProductsToOtherWishlistHandler;
use Sylius\WishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicatorInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;

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
