<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Duplicator;

use BitBag\SyliusWishlistPlugin\Duplicator\WishlistProductsToOtherWishlistDuplicator;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
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
