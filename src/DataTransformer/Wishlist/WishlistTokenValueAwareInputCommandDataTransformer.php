<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\DataTransformer\Wishlist;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistTokenValueAwareInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;

class WishlistTokenValueAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = []): WishlistTokenValueAwareInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];

        /** @var WishlistTokenValueAwareInterface $object */
        $object->setWishlist($wishlist);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof WishlistTokenValueAwareInterface;
    }
}
