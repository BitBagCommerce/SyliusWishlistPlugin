<?php
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactory;
use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactoryInterface;
use PhpSpec\ObjectBehavior;

final class CsvWishlistProductFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CsvWishlistProductFactory::class);
        $this->shouldImplement(CsvWishlistProductFactoryInterface::class);
    }
}
