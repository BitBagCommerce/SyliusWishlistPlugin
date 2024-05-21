<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

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
