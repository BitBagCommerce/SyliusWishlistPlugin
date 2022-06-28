<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactory;
use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Serializer;

final class CsvSerializerFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CsvSerializerFactory::class);
        $this->shouldImplement(CsvSerializerFactoryInterface::class);
    }

    public function it_creates_new_serializer(): void
    {
        $csvSerializer = $this->createNew();
        $csvSerializer->shouldBeAnInstanceOf(Serializer::class);
    }
}
