<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Factory;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Factory\WishlistFactory;
use Sylius\WishlistPlugin\Factory\WishlistFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistFactory::class);
    }

    public function it_implements_wishlist_factory_interface(): void
    {
        $this->shouldHaveType(WishlistFactoryInterface::class);
    }

    public function it_creates_new_wishlist(FactoryInterface $factory, WishlistInterface $wishlist): void
    {
        $factory->createNew()->willReturn($wishlist);

        $this->createNew()->shouldReturn($wishlist);
    }

    public function it_creates_wishlist_for_user(
        FactoryInterface $factory,
        WishlistInterface $wishlist,
        ShopUserInterface $shopUser,
    ): void {
        $factory->createNew()->willReturn($wishlist);

        $wishlist->setName('Wishlist')->shouldBeCalled();
        $wishlist->setShopUser($shopUser)->shouldBeCalled();

        $this->createForUser($shopUser)->shouldReturn($wishlist);
    }
}
