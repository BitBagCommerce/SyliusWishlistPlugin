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
