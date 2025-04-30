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

namespace Sylius\WishlistPlugin\Serializer\Denormalizer;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistTokenValueAwareInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class WishlistTokenValueAwareDenormalizer implements DenormalizerInterface
{
    public function __construct(private DenormalizerInterface $decoratedDenormalizer)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $object = $this->decoratedDenormalizer->denormalize($data, $type, $format, $context);

        if ($object instanceof WishlistTokenValueAwareInterface &&
            isset($context['wishlist']) &&
            $context['wishlist'] instanceof WishlistInterface
        ) {
            $object->setWishlist($context['wishlist']);
        }

        return $object;
    }


    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        if (method_exists($this->decoratedDenormalizer, 'supportsDenormalization') &&
            (new \ReflectionMethod($this->decoratedDenormalizer, 'supportsDenormalization'))->getNumberOfParameters() >= 4) {
            /**
             * @phpstan-ignore-next-line Method signature mismatch is handled by runtime reflection logic for Symfony 6.4/7+ compatibility.
             */
            return  $this->decoratedDenormalizer->supportsDenormalization($data, $type, $format, $context);
        }

        // Fallback for symfony ^6.4
        return $this->decoratedDenormalizer->supportsDenormalization($data, $type, $format);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->decoratedDenormalizer->getSupportedTypes($format);
    }
}
