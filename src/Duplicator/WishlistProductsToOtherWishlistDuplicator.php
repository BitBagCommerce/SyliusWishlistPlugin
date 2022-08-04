<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Duplicator;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Facade\WishlistProductFactoryFacadeInterface;
use BitBag\SyliusWishlistPlugin\Guard\ProductVariantInWishlistGuardInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WishlistProductsToOtherWishlistDuplicator implements WishlistProductsToOtherWishlistDuplicatorInterface
{
    private WishlistProductFactoryFacadeInterface $wishlistProductVariantFactory;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        WishlistProductFactoryFacadeInterface $wishlistProductVariantFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->wishlistProductVariantFactory = $wishlistProductVariantFactory;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, WishlistInterface $destinedWishlist): void
    {
        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $variant = $this->productVariantRepository->find($wishlistProduct['variant']);

            if ($destinedWishlist->hasProductVariant($variant)) {
                $message = $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.product_variant_exists_in_another_wishlist');

                $this->flashBag->add(
                    'error',
                    sprintf("%s".$message, $variant)
                );
            } else {
                $this->wishlistProductVariantFactory->createWithProductVariant($destinedWishlist, $variant);
            }
        }
        $this->wishlistRepository->add($destinedWishlist);
    }
}
