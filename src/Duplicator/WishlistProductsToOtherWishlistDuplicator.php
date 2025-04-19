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

namespace Sylius\WishlistPlugin\Duplicator;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WishlistProductsToOtherWishlistDuplicator implements WishlistProductsToOtherWishlistDuplicatorInterface
{
    public function __construct(
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private WishlistRepositoryInterface $wishlistRepository,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {
    }

    public function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, WishlistInterface $destinedWishlist): void
    {
        foreach ($wishlistProducts as $wishlistProduct) {
            /** @var ?ProductVariantInterface $variant */
            $variant = $this->productVariantRepository->find($wishlistProduct['variant']);

            if (null === $variant) {
                continue;
            }

            if ($destinedWishlist->hasProductVariant($variant)) {
                $message = $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.product_variant_exists_in_another_wishlist');

                /** @var Session $session */
                $session = $this->requestStack->getSession();

                $session->getFlashBag()->add(
                    'error',
                    sprintf('%s' . $message, $variant->getName()),
                );
            } else {
                $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($destinedWishlist, $variant);
                $destinedWishlist->addWishlistProduct($wishlistProduct);
            }
        }
        $this->wishlistRepository->add($destinedWishlist);
    }
}
