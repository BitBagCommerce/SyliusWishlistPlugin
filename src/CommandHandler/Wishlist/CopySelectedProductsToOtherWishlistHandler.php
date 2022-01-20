<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistAction;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CopySelectedProductsToOtherWishlistHandler
{
    private AddProductVariantToWishlistAction $addProductVariantToWishlistAction;

    private RequestStack $requestStack;

    public function __construct(AddProductVariantToWishlistAction $addProductVariantToWishlistAction, RequestStack $requestStack)
    {
        $this->addProductVariantToWishlistAction = $addProductVariantToWishlistAction;
        $this->requestStack = $requestStack;
    }

    public function __invoke(CopySelectedProductsToOtherWishlist $copySelectedProductsToOtherWishlistCommand): void
    {
        $destinedWishlistId = $copySelectedProductsToOtherWishlistCommand->getDestinedWishlistId();

        $currentRequest = $this->requestStack->getCurrentRequest();

        $this->copyWishlistProductsToOtherWishlist($copySelectedProductsToOtherWishlistCommand->getWishlistProducts(), $currentRequest);

        $this->addProductVariantToWishlistAction->__invoke($destinedWishlistId, $currentRequest);
    }

    private function copyWishlistProductsToOtherWishlist(Collection $wishlistProducts, Request $request): void
    {
        $variantIds = [];

        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $variantIds[] = $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId();
        }

        $request->attributes->set('variantId', $variantIds);
    }
}
