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

namespace spec\Sylius\WishlistPlugin\Processor;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;
use Sylius\WishlistPlugin\Processor\SelectedWishlistProductsProcessor;
use Sylius\WishlistPlugin\Processor\SelectedWishlistProductsProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;

final class SelectedWishlistProductsProcessorSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SelectedWishlistProductsProcessor::class);
        $this->shouldImplement(SelectedWishlistProductsProcessorInterface::class);
    }

    public function it_returns_selected_wishlist_items(
        WishlistItem $wishlistItem,
        WishlistItem $wishlistItem2,
    ): void {
        $wishlistItem->isSelected()->willReturn(false);
        $wishlistItem2->isSelected()->willReturn(true);

        $formData = new ArrayCollection([
            $wishlistItem->getWrappedObject(),
            $wishlistItem2->getWrappedObject(),
        ]);

        $this->createSelectedWishlistProductsCollection($formData)
            ->first()
            ->shouldBeLike($wishlistItem2);
    }

    public function it_returns_empty_collection_if_parameter_is_empty(): void
    {
        $formData = new ArrayCollection();

        $this->createSelectedWishlistProductsCollection($formData)
            ->count()
            ->shouldBe(0);
    }
}
