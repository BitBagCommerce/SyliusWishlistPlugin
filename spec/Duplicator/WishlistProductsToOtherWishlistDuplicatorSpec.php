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
use BitBag\SyliusWishlistPlugin\Facade\WishlistProductFactoryFacadeInterface;
use BitBag\SyliusWishlistPlugin\Guard\ProductVariantInWishlistGuardInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WishlistProductsToOtherWishlistDuplicatorSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryFacadeInterface $wishlistProductVariantFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void {
        $this->beConstructedWith(
            $wishlistProductVariantFactory,
            $productVariantRepository,
            $wishlistRepository,
            $flashBag,
            $translator
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
        WishlistRepositoryInterface $wishlistRepository
    ): void {
        $productVariantRepository->find("1")->willReturn($variant1);
        $productVariantRepository->find("24")->willReturn($variant2);

        $destinedWishlist->hasProductVariant($variant1)->shouldBeCalled();
        $destinedWishlist->hasProductVariant($variant2)->shouldBeCalled();

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
