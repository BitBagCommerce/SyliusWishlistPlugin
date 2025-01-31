<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\DataTransformer\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\WishlistTokenValueAwareInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

final class WishlistTokenValueAwareInputCommandDataTransformer
{
    public const OBJECT_TO_POPULATE = 'object_to_populate';

    /**
     * @param WishlistTokenValueAwareInterface|mixed $object
     */
    public function transform(
        $object,
        string $to,
        array $context = [],
    ): WishlistTokenValueAwareInterface {
        /** @var WishlistInterface $wishlist */
        $wishlist = $context[self::OBJECT_TO_POPULATE];

        $object->setWishlist($wishlist);

        return $object;
    }

    /**
     * @param WishlistTokenValueAwareInterface|mixed $object
     */
    public function supportsTransformation($object): bool
    {
        return $object instanceof WishlistTokenValueAwareInterface;
    }
}
