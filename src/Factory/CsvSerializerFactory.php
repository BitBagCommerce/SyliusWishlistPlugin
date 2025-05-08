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

namespace Sylius\WishlistPlugin\Factory;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class CsvSerializerFactory implements CsvSerializerFactoryInterface
{
    public function createNew(): Serializer
    {
        return new Serializer(
            [new ArrayDenormalizer(), new ObjectNormalizer()],
            [new CsvEncoder()],
        );
    }
}
