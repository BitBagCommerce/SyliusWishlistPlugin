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

namespace Sylius\WishlistPlugin\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;

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
