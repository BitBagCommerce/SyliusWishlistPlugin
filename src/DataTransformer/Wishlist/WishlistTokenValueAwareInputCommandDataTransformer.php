<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\DataTransformer\Wishlist;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistTokenValueAwareInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;

final class WishlistTokenValueAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    /**
     * @param WishlistTokenValueAwareInterface|mixed $object
     */
    public function transform(
        $object,
        string $to,
        array $context = [],
    ): WishlistTokenValueAwareInterface {
        /** @var WishlistInterface $wishlist */
        $wishlist = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];

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
