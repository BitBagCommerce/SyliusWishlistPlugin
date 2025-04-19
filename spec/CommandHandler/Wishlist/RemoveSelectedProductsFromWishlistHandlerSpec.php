<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\RemoveSelectedProductsFromWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistProductNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class RemoveSelectedProductsFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $wishlistProductManager,
    ): void {
        $this->beConstructedWith(
            $productVariantRepository,
            $wishlistProductManager,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveSelectedProductsFromWishlistHandler::class);
    }

    public function it_removes_selected_products_from_wishlist(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
        ProductVariantInterface $productVariant,
    ): void {
        $removeSelectedProductsCommand = new RemoveSelectedProductsFromWishlist(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $productVariant->getId()->willReturn(1);
        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getVariant()->willReturn($productVariant);
        $productVariantRepository->find($productVariant)->willReturn($productVariant);

        $this->__invoke($removeSelectedProductsCommand);
    }

    public function it_throws_exception_when_variant_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $removeSelectedProductsCommand = new RemoveSelectedProductsFromWishlist(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getVariant()->willReturn(null);
        $productVariantRepository->find(null)->willReturn(null);

        $this->shouldThrow(ProductNotFoundException::class)->during('__invoke', [$removeSelectedProductsCommand]);
    }

    public function it_throws_exception_when_wishlist_product_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $removeSelectedProductsCommand = new RemoveSelectedProductsFromWishlist(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $wishlistItem->getWishlistProduct()->willReturn(null);

        $this->shouldThrow(WishlistProductNotFoundException::class)->during('__invoke', [$removeSelectedProductsCommand]);
    }
}
