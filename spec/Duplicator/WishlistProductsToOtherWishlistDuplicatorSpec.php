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

namespace spec\Sylius\WishlistPlugin\Duplicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\WishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicator;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WishlistProductsToOtherWishlistDuplicatorSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RequestStack $requestStack,
        TranslatorInterface $translator,
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $productVariantRepository,
            $wishlistRepository,
            $requestStack,
            $translator,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductsToOtherWishlistDuplicator::class);
    }

    public function it_copy_wishlist_products(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        WishlistInterface $destinedWishlist,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct1,
        WishlistProductInterface $wishlistProduct2,
    ): void {
        $productVariantRepository->find('1')->willReturn($variant1);
        $productVariantRepository->find('24')->willReturn($variant2);

        $destinedWishlist->hasProductVariant($variant1)->shouldBeCalled();
        $destinedWishlist->hasProductVariant($variant2)->shouldBeCalled();

        $wishlistProductFactory->createForWishlistAndVariant($destinedWishlist, $variant1)->willReturn($wishlistProduct1);
        $wishlistProductFactory->createForWishlistAndVariant($destinedWishlist, $variant2)->willReturn($wishlistProduct2);

        $destinedWishlist->addWishlistProduct($wishlistProduct1)->shouldBeCalled();
        $destinedWishlist->addWishlistProduct($wishlistProduct2)->shouldBeCalled();

        $wishlistRepository->add($destinedWishlist)->shouldBeCalledOnce();

        $this->copyWishlistProductsToOtherWishlist(new ArrayCollection([
            [
                'variant' => '1',
            ],
            [
                'variant' => '24',
            ],
        ]), $destinedWishlist);
    }
}
