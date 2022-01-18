<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Processor;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
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
