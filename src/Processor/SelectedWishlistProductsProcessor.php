<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Processor;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class SelectedWishlistProductsProcessor implements SelectedWishlistProductsProcessorInterface
{
    public function createSelectedWishlistProductsCollection(Collection $formData): ArrayCollection
    {
        $selectedProducts = new ArrayCollection();

        /** @var WishlistItem $wishlistItem */
        foreach ($formData as $wishlistItem) {
            if ($wishlistItem->isSelected()) {
                $selectedProducts->add($wishlistItem);
            }
        }

        return $selectedProducts;
    }
}
