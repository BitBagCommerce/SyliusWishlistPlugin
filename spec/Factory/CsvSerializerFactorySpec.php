<?php
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactory;
use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use PhpSpec\ObjectBehavior;

final class CsvSerializerFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CsvSerializerFactory::class);
        $this->shouldImplement(CsvSerializerFactoryInterface::class);
    }


}
