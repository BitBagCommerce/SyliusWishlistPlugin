<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Services\Copyist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Facade\WishlistProductFactoryFacadeInterface;
use BitBag\SyliusWishlistPlugin\Guard\ProductVariantInWishlistGuardInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Services\Copyist\WishlistProductsToOtherWishlistCopyist;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class WishlistProductsToOtherWishlistCopyistSpec extends ObjectBehavior
{
    public function let(
        ProductVariantInWishlistGuardInterface $productVariantInWishlistGuard,
        WishlistProductFactoryFacadeInterface $wishlistProductVariantFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $this->beConstructedWith(
            $productVariantInWishlistGuard,
            $wishlistProductVariantFactory,
            $productVariantRepository,
            $wishlistRepository
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductsToOtherWishlistCopyist::class);
    }

    public function it_copy_wishlist_products(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        ProductVariantInWishlistGuardInterface $productVariantInWishlistGuard,
        WishlistInterface $destinedWishlist,
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $productVariantRepository->find("1")->willReturn($variant1);
        $productVariantRepository->find("24")->willReturn($variant2);

        $productVariantInWishlistGuard->check($destinedWishlist, $variant1)->shouldBeCalledOnce();
        $productVariantInWishlistGuard->check($destinedWishlist, $variant2)->shouldBeCalledOnce();

        $wishlistRepository->add($destinedWishlist)->shouldBeCalledOnce();

        $this->copyWishlistProductsToOtherWishlist(new ArrayCollection([
            [
                "variant" => "1"
            ],
            [
                "variant" => "24"
            ]
        ]), $destinedWishlist);
    }
}
