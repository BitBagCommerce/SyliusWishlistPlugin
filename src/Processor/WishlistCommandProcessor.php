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

final class WishlistCommandProcessor implements WishlistCommandProcessorInterface
{
    public function createWishlistItemsCollection(Collection $wishlistProducts): ArrayCollection
    {
        $commandsArray = new ArrayCollection();

        foreach ($wishlistProducts as $wishlistProductItem) {
            $wishlistProductCommand = new WishlistItem();
            $wishlistProductCommand->setWishlistProduct($wishlistProductItem);
            $commandsArray->add($wishlistProductCommand);
        }

        return $commandsArray;
    }
}
