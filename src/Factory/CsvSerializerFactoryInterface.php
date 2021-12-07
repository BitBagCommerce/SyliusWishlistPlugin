<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Factory;

use Symfony\Component\Serializer\Serializer;

interface CsvSerializerFactoryInterface
{
    public function createNew(): Serializer;
}
